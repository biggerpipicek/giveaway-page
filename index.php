<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>Soutěž</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.step {
    display: none;
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease;
}

.step.active {
    display: block;
    opacity: 1;
    transform: translateX(0);
}
</style>
</head>

<body class="bg-dark text-light">

<div class="container mt-5">
    <div class="card p-4 bg-secondary">

        <!-- Progress -->
        <div class="progress mb-4">
            <div id="progressBar" class="progress-bar" style="width: 33%"></div>
        </div>

        <!-- STEP 1 -->
        <div id="step1" class="step active">
            <h2>Pojď se zapojit do soutěže!</h2>
            <p>Úplně ZDARMA 🎁</p>
            <button class="btn btn-primary w-100" onclick="nextStep(2)">Pokračovat</button>
        </div>

        <!-- STEP 2 -->
        <div id="step2" class="step">
            <h3>Vyplň údaje</h3>

            <div class="mb-3">
                <label>Jméno / Přezdívka</label>
                <input type="text" id="name" class="form-control">
            </div>

            <div class="mb-3">
                <label>Způsob kontaktu</label>
                <select id="contact_type" class="form-control" onchange="validateStep2()">
                    <option value="email">Email</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Kontakt</label>
                <input type="text" id="contact_value" class="form-control" oninput="validateStep2()">
                <small id="contactHelp" class="text-warning"></small>
            </div>

            <button id="step2btn" class="btn btn-primary w-100" disabled onclick="nextStep(3)">Pokračovat</button>
        </div>

        <!-- STEP 3 -->
        <div id="step3" class="step">
            <h3>Podmínky soutěže</h3>

            <p>1. Dej odběr na YouTube</p>
            <p>2. Napiš komentář pod video</p>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="subCheck" onchange="validateStep3()">
                <label class="form-check-label">Dal jsem odběr</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="commentCheck" onchange="validateStep3()">
                <label class="form-check-label">Napsal jsem komentář</label>
            </div>

            <button id="submitBtn" class="btn btn-success w-100" disabled onclick="submitForm()">Zapojit se</button>
        </div>

        <!-- SUCCESS -->
        <div id="success" class="step text-center">
            <h2>🎉 Hotovo!</h2>
            <p>Díky za zapojení do soutěže.</p>
        </div>

    </div>
</div>

<script>
let currentStep = 1;

// STEP SWITCH
function nextStep(step) {
    saveData();

    document.querySelectorAll(".step").forEach(s => s.classList.remove("active"));
    document.getElementById("step" + step).classList.add("active");

    currentStep = step;
    updateProgress();
}

// PROGRESS BAR
function updateProgress() {
    let percent = (currentStep / 3) * 100;
    document.getElementById("progressBar").style.width = percent + "%";
}

// VALIDACE STEP 2
function validateStep2() {
    let type = document.getElementById("contact_type").value;
    let value = document.getElementById("contact_value").value;
    let help = document.getElementById("contactHelp");

    let valid = false;

    if (type === "email") {
        valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        help.innerText = valid ? "" : "Zadej platný email";
    } else {
        valid = value.length > 2;
        help.innerText = valid ? "" : "Zadej platný údaj";
    }

    document.getElementById("step2btn").disabled = !valid;
}

// VALIDACE STEP 3
function validateStep3() {
    let sub = document.getElementById("subCheck").checked;
    let comment = document.getElementById("commentCheck").checked;

    document.getElementById("submitBtn").disabled = !(sub && comment);
}

// LOCAL STORAGE SAVE
function saveData() {
    localStorage.setItem("name", document.getElementById("name").value);
    localStorage.setItem("contact_type", document.getElementById("contact_type").value);
    localStorage.setItem("contact_value", document.getElementById("contact_value").value);
}

// LOAD DATA
window.onload = () => {
    document.getElementById("name").value = localStorage.getItem("name") || "";
    document.getElementById("contact_type").value = localStorage.getItem("contact_type") || "email";
    document.getElementById("contact_value").value = localStorage.getItem("contact_value") || "";
    validateStep2();
};

// SUBMIT
function submitForm() {
    saveData();

    fetch("submit.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `name=${encodeURIComponent(localStorage.getItem("name"))}&contact_type=${localStorage.getItem("contact_type")}&contact_value=${encodeURIComponent(localStorage.getItem("contact_value"))}`
    })
    .then(res => res.text())
    .then(data => {
        console.log(data);

        // 👇 TADY redirect
        window.location.href = "success.html";
    });
    }
</script>

</body>
</html>