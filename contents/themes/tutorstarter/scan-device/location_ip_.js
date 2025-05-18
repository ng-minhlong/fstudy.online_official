async function checkLocationAndIpAddress() {
    try {
        // Fetch IP address and location details
        const response = await fetch('https://ipinfo.io/json?token=1e3887629fcd4e');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Extract and format information
        const ipAddress = data.ip || 'Unknown';
        let location = 'Unknown';
        
        if (data.city && data.region && data.country) {
            location = `${data.city}, ${data.region}, ${data.country}`;
        } else if (data.loc) {
            location = data.loc;
        }

        console.log('Thông tin thiết bị:');
        console.log('- IP Address:', ipAddress);
        console.log('- Location:', location);
        console.log('- ISP:', data.org || 'Unknown');
        console.log('- Timezone:', data.timezone || 'Unknown');

        return { ipAddress, location };
    } catch (error) {
        console.error('Lỗi khi lấy thông tin vị trí và địa chỉ IP:', error);
        return {
            ipAddress: 'Error',
            location: 'Error'
        };
    }
}