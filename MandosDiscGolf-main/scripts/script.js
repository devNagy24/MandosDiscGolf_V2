

window.onload = function () {
    // get the button element
    let button = document.querySelector("#button");
    console.log("test");
    //add an event listener for button click
    //that displays breed properties
    button.addEventListener("click", validateEmail);
    
}

// default Bootstrap 5 function for form validation
function formValidation() {


    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation');


    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('click', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                }

                form.classList.add('was-validated');
            }, false)
        });

};


// if the name of a player is equal to "Bye" remove the required
// attribute frm the email input
function validateEmail() {

    const players = document.querySelectorAll('fieldset');


    players.forEach(myFunc);

    function myFunc(item) {
        const nameVal = item.querySelector('input[type="text"]');
        const emailVal = item.querySelector('input[type="email"]');

        if (nameVal.value.toLowerCase() === "bye") {
            emailVal.removeAttribute("required");
        }
    }


}

// for player search page
var prevRow = null; // variable to keep track of the previously clicked row

var rows = document.querySelectorAll('.clickable-row');
for (var i = 0; i < rows.length; i++) {
    rows[i].addEventListener('click', function() {
        if (prevRow !== null) {
            prevRow.style.backgroundColor = ''; // remove background color from previously clicked row
        }
        this.style.backgroundColor = 'palegreen'; // set background color of current row
        prevRow = this; // update prevRow variable to current row

        // get values of name and PDGA number columns
        var name = this.querySelector('nameColumnValue').innerText;
        var pdga_number = this.querySelector('pdgaNumberColumnValue').innerText;
        console.log(name);
        // set values of hidden input fields
        document.getElementById('nameHidden').value = name;
        document.getElementById('pdgaNumber').value = pdga_number;

    });
}