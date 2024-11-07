
// Tüm .dev elemanlarını seç
const hoverAreas = document.querySelectorAll('.dev');

hoverAreas.forEach(hoverArea => {
    // Her dev için, ilgili tooltip'i bul
    const tooltip = hoverArea.querySelector('.tooltip');
    
    hoverArea.addEventListener('mousemove', (event) => {
        const mouseX = event.pageX;
        const mouseY = event.pageY;

        // Tooltip'in konumunu mouse koordinatlarına göre ayarla
        tooltip.style.left = `${mouseX-20}px`;
        tooltip.style.top = `${mouseY-50}px`; 
    });
});
