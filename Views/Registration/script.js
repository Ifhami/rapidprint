const form = document.querySelector("form");
const eField = form.querySelector(".email"),
      eInput = eField.querySelector("input"),
      pField = form.querySelector(".password"),
      pInput = pField.querySelector("input"),
      nameField = form.querySelector(".fullname"),
      nameInput = nameField.querySelector("input"),
      confirmField = form.querySelector(".confirm-password"),
      confirmInput = confirmField.querySelector("input");

form.onsubmit = (e) => {
  e.preventDefault();

  (nameInput.value == "") ? nameField.classList.add("shake", "error") : checkName();
  (eInput.value == "") ? eField.classList.add("shake", "error") : checkEmail();
  (pInput.value == "") ? pField.classList.add("shake", "error") : checkPass();
  (confirmInput.value == "" || confirmInput.value !== pInput.value) ? confirmField.classList.add("shake", "error") : checkConfirmPass();

  setTimeout(() => {
    nameField.classList.remove("shake");
    eField.classList.remove("shake");
    pField.classList.remove("shake");
    confirmField.classList.remove("shake");
  }, 500);

  nameInput.onkeyup = () => { checkName(); };
  eInput.onkeyup = () => { checkEmail(); };
  pInput.onkeyup = () => { checkPass(); };
  confirmInput.onkeyup = () => { checkConfirmPass(); };

  function checkName() {
    if (nameInput.value == "") {
      nameField.classList.add("error");
      nameField.classList.remove("valid");
    } else {
      nameField.classList.remove("error");
      nameField.classList.add("valid");
    }
  }

  function checkEmail() {
    const pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!eInput.value.match(pattern)) {
      eField.classList.add("error");
      eField.classList.remove("valid");
      let errorTxt = eField.querySelector(".error-txt");
      errorTxt.innerText = (eInput.value != "") ? "Enter a valid email address" : "Email can't be blank";
    } else {
      eField.classList.remove("error");
      eField.classList.add("valid");
    }
  }

  function checkPass() {
    if (pInput.value == "") {
      pField.classList.add("error");
      pField.classList.remove("valid");
    } else {
      pField.classList.remove("error");
      pField.classList.add("valid");
    }
  }

  function checkConfirmPass() {
    if (confirmInput.value == "" || confirmInput.value !== pInput.value) {
      confirmField.classList.add("error");
      confirmField.classList.remove("valid");
    } else {
      confirmField.classList.remove("error");
      confirmField.classList.add("valid");
    }
  }

  if (!eField.classList.contains("error") && !pField.classList.contains("error") && !nameField.classList.contains("error") && !confirmField.classList.contains("error")) {
    window.location.href = form.getAttribute("action");
  }
};
