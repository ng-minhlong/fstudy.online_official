

function formatTime(seconds) {
        let hours = Math.floor(seconds / 3600);
        let minutes = Math.floor((seconds % 3600) / 60);
        let remainingSeconds = seconds % 60;

        let formattedTime = '';

        if (hours > 0) {
            formattedTime += hours + ' giờ ';
        }

        if (minutes > 0) {
            formattedTime += minutes + ' phút ';
         }

        if (remainingSeconds > 0) {
            formattedTime += remainingSeconds + ' giây ';
        }

        return formattedTime.trim();
    }


function secondsToHMS(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let remainingSeconds = seconds % 60;
            let formattedTime =
                (hours < 10 ? "0" : "") + hours + ":" +
                (minutes < 10 ? "0" : "") + minutes + ":" +
                (remainingSeconds < 10 ? "0" : "") + remainingSeconds;
            return formattedTime;
            }

            let countdownInterval;
            let isCountdownRunning = false;
function startCountdown() {
    countdownInterval = setInterval(function () {
        isCountdownRunning = true;
        countdownValue--;

        document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);

        if (isFullTest === true && orderedModules.length > 0 && partMod < orderedModules.length) {
            const currentModule = orderedModules[partMod];
            moduleTimeRemaining[currentModule] = countdownValue;
        }

        if (countdownValue <= 0) {
            clearInterval(countdownInterval);
            isCountdownRunning = false;

            if (isFullTest === true && partMod < orderedModules.length - 1) {
                const currentModule = orderedModules[partMod];
                const nextModule = orderedModules[partMod + 1];

                Swal.fire({
                    title: "Time's Up for Current Module",
                    text: `The ${currentModule} module has ended. You'll now move to the next module!`,
                    icon: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    allowOutsideClick: false,
                    confirmButtonText: "Continue to Next Module"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (nextModule === "Section 1: Math" && currentModule === "Section 2: Reading And Writing") {
                            startBreakTimer();
                        } else {
                            switchToNextModule(nextModule);
                            startCountdown();
                        }


                        //partMod++;
                       
                    }
                });
                return;
            }

            Swal.fire({
                title: "Time's Up!",
                text: "Your test will be automatically submitted now.",
                icon: "warning",
                showCancelButton: false,
                confirmButtonColor: "#3085d6",
                allowOutsideClick: false,
                confirmButtonText: "Submit Now"
            }).then((result) => {
                if (result.isConfirmed) {
                    let timerInterval;
                    Swal.fire({
                        title: "Submitting Your Test",
                        html: "Please wait while we process your answers...",
                        timer: 2000,
                        allowOutsideClick: false,
                        showCloseButton: false,
                        timerProgressBar: true,
                        didOpen: () => Swal.showLoading(),
                        willClose: () => {
                            clearInterval(timerInterval);
                            submitButton();
                            DoingTest = false;
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            submitAnswerAndGenerateLink();
                        }
                    });
                }
            });
        }
    }, 1000);
}
