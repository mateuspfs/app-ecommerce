document.addEventListener('DOMContentLoaded', function() {
    function previewImages(input) {
        console.log('chamando preview');
        var modalContent = input.closest('.modal-content');
        var previewClass = input.name === 'img' ? '.main-image-preview' : '.gallery-image-preview';
        var preview = modalContent.querySelector(previewClass);
        var selectedImagesInput = modalContent.querySelector('.selected-images');
        var formData = new FormData();

        preview.innerHTML = '';

        if (input.files) {
            var files = Array.from(input.files);

            files.forEach(function(file) {
                formData.append(input.name, file);
                renderImagePreview(file, preview);
            });

            selectedImagesInput.value = files.map(file => file.name).join(',');
        }
    }

    function renderImagePreview(file, preview) {
        console.log('render preview');
        var reader = new FileReader();
        reader.onload = function(event) {
            var imageContainer = document.createElement('div');
            imageContainer.classList.add('col-6', 'col-md-4', 'col-lg-3', 'mb-3', 'position-relative');

            var image = document.createElement('img');
            image.src = event.target.result;
            image.classList.add('img-thumbnail');
            image.style.width = '100%';

            var removeButton = document.createElement('button');
            removeButton.textContent = 'Remover';
            removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
            removeButton.addEventListener('click', function() {
                imageContainer.remove();
                removeImageFromInput(file.name);
            });

            imageContainer.appendChild(image);
            imageContainer.appendChild(removeButton);
            preview.appendChild(imageContainer);
        };

        reader.readAsDataURL(file);
    }

    function removeImageFromInput(fileName) {
        var inputs = document.querySelectorAll('.imagens-input');
        inputs.forEach(function(input) {
            var dataTransfer = new DataTransfer();
            Array.from(input.files).forEach(function(file) {
                if (file.name !== fileName) {
                    dataTransfer.items.add(file);
                }
            });
            input.files = dataTransfer.files;
        });
    }

    var inputs = document.querySelectorAll('.imagens-input');
    inputs.forEach(function(input) {
        input.addEventListener('change', function() {
            previewImages(this);
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            var imageContainer = event.target.parentElement;
            imageContainer.remove();
        }
    });

    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            var imageId = this.getAttribute('data-id');
            var imageItem = this.closest('.image-item');

            if (confirm('Tem certeza que deseja excluir esta imagem?')) {
                $.ajax({
                    url: apiDeleteImg + imageId,
                    method: 'GET',
                    success: function(response) {
                        imageItem.remove();
                    },
                    error: function() {
                        Swal.fire('Erro!', "ocorreu um erro ao excluir, tente novamente", 'error');                    
                    }
                });
            }
        });
    });
});
