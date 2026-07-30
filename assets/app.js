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
document.querySelector('.print-receipt')?.addEventListener('click', () => window.print());
