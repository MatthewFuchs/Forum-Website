document.getElementById('signupForm').addEventListener('submit', function(event) {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    
    // Example validation
    if(password.length < 8) {
      alert('Password must be at least 8 characters long.');
      event.preventDefault(); // Prevent form submission
    }
    // Add more validation as needed
  });
  