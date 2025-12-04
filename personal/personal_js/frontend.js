const API_URL = "http://localhost:3000/api";

async function postData(url = '', data = {}) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    return res.json();
}

// --- Registration ---
const registerForm = document.getElementById("registerForm");
if (registerForm) {
    registerForm.addEventListener("submit", async e => {
        e.preventDefault();
        const name = document.getElementById("registerName").value;
        const email = document.getElementById("registerEmail").value;
        const password = document.getElementById("registerPassword").value;
        const res = await postData(`${API_URL}/register`, { name, email, password });
        alert(res.message);
        if (res.message === "Registration successful") window.location.href = "index.html";
    });
}

// --- Login ---
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", async e => {
        e.preventDefault();
        const email = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPassword").value;
        const res = await postData(`${API_URL}/login`, { email, password });
        alert(res.message);
    });
}

// --- Forgot Password ---
const forgotForm = document.getElementById("forgotForm");
if (forgotForm) {
    forgotForm.addEventListener("submit", async e => {
        e.preventDefault();
        const email = document.getElementById("forgotEmail").value;
        const res = await postData(`${API_URL}/forgot`, { email });
        alert(res.message);
    });
}

// --- Reset Password ---
const resetForm = document.getElementById("resetForm");
if (resetForm) {
    resetForm.addEventListener("submit", async e => {
        e.preventDefault();
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get("token");
        const email = urlParams.get("email");
        const newPassword = document.getElementById("resetPassword").value;
        const res = await postData(`${API_URL}/reset`, { email, token, newPassword });
        alert(res.message);
        if (res.message === "Password reset successful") window.location.href = "index.html";
    });
}
