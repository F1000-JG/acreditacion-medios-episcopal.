const foto = document.querySelector('#foto');
const preview = document.querySelector('#fotoPreview');
const icon = document.querySelector('#fotoIcon');

foto?.addEventListener('change', () => {
  const file = foto.files?.[0];
  if (!file) return;
  preview.src = URL.createObjectURL(file);
  preview.hidden = false;
  icon.hidden = true;
});

const dui = document.querySelector('#dui');
dui?.addEventListener('input', () => {
  const numbers = dui.value.replace(/\D/g, '').slice(0, 9);
  dui.value = numbers.length > 8 ? `${numbers.slice(0, 8)}-${numbers.slice(8)}` : numbers;
});

document.querySelector('.modal-close')?.addEventListener('click', () => {
  document.querySelector('.success-modal')?.remove();
});

document.querySelector('.print-credential')?.addEventListener('click', () => {
  document.body.classList.add('printing-credential');
  window.print();
  setTimeout(() => document.body.classList.remove('printing-credential'), 300);
});

document.querySelector('.download-credential')?.addEventListener('click', async (event) => {
  const button = event.currentTarget;
  const credential = document.querySelector('#credential');
  if (!credential || !window.html2canvas || !window.jspdf) return;
  const previousText = button.textContent;
  button.disabled = true;
  button.textContent = 'Creando PDF…';
  try {
    const canvas = await html2canvas(credential, { scale: 3, useCORS: true, backgroundColor: '#ffffff' });
    const image = canvas.toDataURL('image/png');
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: [86, 132] });
    pdf.addImage(image, 'PNG', 0, 0, 86, 132);
    pdf.save(`${credential.dataset.file || 'credencial-prensa'}.pdf`);
  } finally {
    button.disabled = false;
    button.textContent = previousText;
  }
});

document.querySelector('.print-dashboard')?.addEventListener('click', () => window.print());

document.querySelector('.copy-public-link')?.addEventListener('click', async (event) => {
  const input = document.querySelector('#publicFormLink');
  if (!input) return;
  await navigator.clipboard.writeText(input.value);
  const button = event.currentTarget;
  const previousText = button.textContent;
  button.textContent = 'Enlace copiado';
  setTimeout(() => { button.textContent = previousText; }, 1800);
});

document.querySelector('.download-dashboard')?.addEventListener('click', async (event) => {
  const button = event.currentTarget;
  const dashboard = document.querySelector('.dashboard-report');
  if (!dashboard || !window.html2canvas || !window.jspdf) return;
  const previousText = button.textContent;
  button.disabled = true;
  button.textContent = 'Creando PDF…';
  try {
    const canvas = await html2canvas(dashboard, { scale: 1.5, useCORS: true, backgroundColor: '#ffffff' });
    const image = canvas.toDataURL('image/jpeg', 0.92);
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
    const pageW = 297;
    const pageH = Math.min(200, canvas.height * pageW / canvas.width);
    pdf.addImage(image, 'JPEG', 0, 0, pageW, pageH);
    pdf.save('reporte-medios-ordenacion-episcopal.pdf');
  } finally {
    button.disabled = false;
    button.textContent = previousText;
  }
});
