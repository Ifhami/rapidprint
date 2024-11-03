
// Set the inactivity time limit in milliseconds
const idleTimeLimit = 300000;
let idleTimer;

// Reset the timer whenever user activity is detected
function resetTimer() {
  clearTimeout(idleTimer); // Clear any previous timer
  idleTimer = setTimeout(() => {
    alert("Session timed out due to inactivity.");
    window.location.href = "../../Views/Login/login.php"; // Redirect to login page
  }, idleTimeLimit);
}

// Monitor for user activity events
window.onload = resetTimer;       // Start timer on page load
document.onmousemove = resetTimer; // Reset timer on mouse movement
document.onkeypress = resetTimer;  // Reset timer on key press
document.onscroll = resetTimer;    // Reset timer on scroll

// Initialize the timer when the script loads
resetTimer();