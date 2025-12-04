import express from 'express';
import bcrypt from 'bcrypt';
import nodemailer from 'nodemailer';
import bodyParser from 'body-parser';
import crypto from 'crypto';
import cors from 'cors';

const app = express();
app.use(bodyParser.json());
app.use(cors());

const PORT = 3000;

// Temporary "database"
let users = {}; // { email: { name, passwordHash, resetToken } }

// Configure Nodemailer (simulate email sending)
const transporter = nodemailer.createTransport({
    host: "smtp.ethereal.email", // free testing SMTP
    port: 587,
    auth: {
        user: process.env.EMAIL_USER,
        pass: process.env.EMAIL_PASS
    }
});

// --- Registration ---
app.post('/api/register', async (req, res) => {
    const { name, email, password } = req.body;
    if (users[email]) return res.status(400).json({ message: "User already exists" });
    
    const passwordHash = await bcrypt.hash(password, 10);
    users[email] = { name, passwordHash };
    res.json({ message: "Registration successful" });
});

// --- Login ---
app.post('/api/login', async (req, res) => {
    const { email, password } = req.body;
    const user = users[email];
    if (!user) return res.status(400).json({ message: "User not found" });

    const valid = await bcrypt.compare(password, user.passwordHash);
    if (!valid) return res.status(400).json({ message: "Incorrect password" });

    res.json({ message: `Welcome ${user.name}!` });
});

// --- Forgot Password ---
app.post('/api/forgot', (req, res) => {
    const { email } = req.body;
    const user = users[email];
    if (!user) return res.status(400).json({ message: "Email not registered" });

    // Generate reset token
    const resetToken = crypto.randomBytes(20).toString('hex');
    user.resetToken = resetToken;

    // Send email
    const resetLink = `http://localhost:5500/frontend/reset.html?token=${resetToken}&email=${email}`;
    transporter.sendMail({
        from: `"Auth System" <${process.env.EMAIL_USER}>`,
        to: email,
        subject: "Password Reset",
        text: `Reset your password using this link: ${resetLink}`
    }).then(() => {
        res.json({ message: "Password reset link sent to email" });
    }).catch(err => {
        res.status(500).json({ message: "Error sending email", err });
    });
});

// --- Reset Password ---
app.post('/api/reset', async (req, res) => {
    const { email, token, newPassword } = req.body;
    const user = users[email];
    if (!user || user.resetToken !== token) return res.status(400).json({ message: "Invalid token" });

    user.passwordHash = await bcrypt.hash(newPassword, 10);
    delete user.resetToken;
    res.json({ message: "Password reset successful" });
});

app.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));
