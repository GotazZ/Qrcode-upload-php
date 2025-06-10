// Récupère les éléments DOM qui vont être utilisés dans le script
const dropZone = document.getElementById('drop-zone'); // Zone de glisser-déposer
const fileInput = document.getElementById('file-input'); // Champ d'input pour sélectionner un fichier
const fileNameDisplay = document.getElementById('file-name'); // Élément où le nom du fichier sera affiché

// Lorsque l'utilisateur clique sur la zone de dépôt, on déclenche un clic sur l'input file
dropZone.addEventListener('click', () => fileInput.click());

// Permet au navigateur d'accepter le drop, sinon il l'ignore par défaut
dropZone.addEventListener('dragover', (e) => {
  e.preventDefault();
  dropZone.classList.add('dragover'); // Style visuel pendant le survol
});

// Empêche la réinitialisation visuelle si l'utilisateur sort le fichier sans le déposer
dropZone.addEventListener('dragleave', () => {
  dropZone.classList.remove('dragover');
});

// Permet d'attribuer manuellement le fichier déposé à l'input (sinon il ne le reçoit pas)
dropZone.addEventListener('drop', (e) => {
  e.preventDefault();
  dropZone.classList.remove('dragover');

  const files = e.dataTransfer.files;
  if (files.length > 0) {
    fileInput.files = files; // Injection directe dans le champ file pour que le formulaire fonctionne
    fileNameDisplay.textContent = `Fichier sélectionné : ${files[0].name}`;
  }
});

// Lorsque l'utilisateur sélectionne un fichier via l'input file (bouton de sélection)
fileInput.addEventListener('change', () => {
  if (fileInput.files.length > 0) { // Si un fichier a été sélectionné
    fileNameDisplay.textContent = `Fichier sélectionné : ${fileInput.files[0].name}`; // Affiche le nom du fichier sélectionné
  }
});
