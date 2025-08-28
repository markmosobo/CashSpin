document.addEventListener('DOMContentLoaded', function () {
  // Select all buttons inside the #radioBtn container
  const buttons = document.querySelectorAll('#radioBtn .btn');

  // Select the spin button
  const spinButton = document.querySelector('.spinBtn');

  // Select the <h4> element to update
  const h4Element = document.querySelector('h4.text-white');

  // Select the result container
  const resultContainer = document.querySelector('.spinner-result');
  const resultText = document.getElementById('spinResult');

  // Add click event listeners to each button
  buttons.forEach(button => {
      button.addEventListener('click', function (event) {
          // Prevent the default behavior of the <a> tag
          event.preventDefault();

          // Remove 'active' class from all buttons
          buttons.forEach(btn => btn.classList.remove('active'));

          // Add 'active' class to the clicked button
          this.classList.add('active');

          // Update the hidden input value
          const hiddenInput = document.getElementById('fun');
          hiddenInput.value = this.getAttribute('data-title');

          // Update the <h4> element with the selected data-title
          if (h4Element) {
              h4Element.textContent = this.getAttribute('data-title');
              h4Element.classList.remove('invisible'); // Make it visible if it was hidden
              h4Element.classList.add('visible'); // Optional: Add a 'visible' class
          }

          // Enable the spin button and add glowing animation
          spinButton.disabled = false; // Enable the button
          spinButton.removeAttribute('disabled'); // Remove the disabled attribute
          spinButton.classList.add('glow'); // Add glowing animation class

          // Log the selected value for debugging
          console.log('Selected value:', hiddenInput.value);
      });
  });

  // Add click event listener to the spin button
  spinButton.addEventListener('click', function () {
      if (!spinButton.disabled) {
          console.log('Spin button clicked!');

          // Trigger the spinner functionality
          const spinWheel = new Spin2WinWheel();
          spinWheel.init({
              data: {
                  svgWidth: 500,
                  svgHeight: 500,
                  wheelSize: 500,
                  centerX: 250,
                  centerY: 250,
                  segmentValuesArray: [
                      { type: "string", value: "Bonus" },
                      { type: "string", value: "X2" },
                      { type: "string", value: "X0.5" },
                      { type: "string", value: "X1" },
                      { type: "string", value: "X10" },
                      { type: "string", value: "X2.5" },
                      { type: "string", value: "X3" },
                      { type: "string", value: "X5" },
                      { type: "string", value: "X4" },
                      { type: "string", value: "X100" },
                      { type: "string", value: "Spin" },
                      { type: "string", value: "X1.5" },
                      { type: "string", value: "X2" },
                      { type: "string", value: "X4" },
                      { type: "string", value: "Bonus" },
                      { type: "string", value: "X1.5" },
                      { type: "string", value: "X10" },
                      { type: "string", value: "X2" },
                      { type: "string", value: "X0" },
                      { type: "string", value: "X5" },
                      { type: "string", value: "X2" },
                      { type: "string", value: "X1" }
                  ],
                  numSpins: 1,
                  minSpinDuration: 3,
                  spinDirection: "cw"
              },
              onResult: function (result) {
                  console.log("Spin result:", result);

                  // Display the result
                  resultText.textContent = result;
                  resultContainer.style.display = 'block';
              },
              onError: function (error) {
                  console.error("Spin error:", error);
              },
              onGameEnd: function (gameData) {
                  console.log("Game ended:", gameData);
              }
          });
      }
  });
});