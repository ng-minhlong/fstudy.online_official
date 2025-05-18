function draw_full_overall_band(xValues, yValues) {
    var barColors = ["red", "green", "blue", "orange", "brown"];

    new Chart("barchartfull", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        max: 9,
                        stepSize: 0.5,
                        callback: function(value) {
                            return Number.isInteger(value) ? value.toFixed(0) : value.toFixed(1);
                        }
                    }
                }]
            },
            legend: { display: false },
            title: {
                display: true,
                text: "Overall Band Scores"
            }
        }
    });
}
