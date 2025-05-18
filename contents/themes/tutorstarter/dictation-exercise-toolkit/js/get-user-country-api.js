fetch('https://ipinfo.io/json?token=1e3887629fcd4e')
.then(response => response.json())
.then(data => {
    const ip = data.ip;
    const country = data.country;

    // Get device information
    const device = navigator.userAgent;

    // Log information to the console
    console.log(`IP Address: ${ip}`);
    console.log(`Country: ${country}`);
    
    console.log(`Device: ${device}`);
  //  country_name	
    document.getElementById("trans-1").textContent = {country_name};


 /*   if (country == "VN")
{
        document.getElementById("trans-1").textContent = 'Okay';
} */
})
.catch(error => console.error('Error fetching IP information:', error));


