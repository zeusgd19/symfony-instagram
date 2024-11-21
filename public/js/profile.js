document.addEventListener('DOMContentLoaded',function() {
const unfollowed = document.getElementById("unfollowed");
    if (unfollowed) {
        unfollowed.addEventListener("click", function () {
            XHR = new XMLHttpRequest();
            const id = document.getElementById("profile-name").getAttribute("data-id");
            XHR.open("POST", `/addFollowing/${id}`);
            XHR.addEventListener("readystatechange", function () {
                if (XHR.readyState !== 4) {
                    return;
                }
                if (XHR.status === 200) {
                    unfollowed.classList.add("hide");
                    document.getElementById("followed").classList.remove("hide");
                    const jsonFolloewers = JSON.parse(XHR.responseText);
                    $('#followers').text(jsonFolloewers.followers + " Followers");
                }
            })
            XHR.send();
        })
    }

    const followed = document.getElementById("followed");
    if (followed) {
        followed.addEventListener("click", function () {
            XHR = new XMLHttpRequest();
            const id = document.getElementById("profile-name").getAttribute("data-id");
            XHR.open("POST", `/removeFollowing/${id}`);
            XHR.addEventListener("readystatechange", function () {
                if (XHR.readyState !== 4) {
                    return;
                }
                if (XHR.status === 200) {
                    followed.classList.add("hide");
                    document.getElementById("unfollowed").classList.remove("hide");
                    const jsonFolloewers = JSON.parse(XHR.responseText);
                    $('#followers').text(jsonFolloewers.followers + " Followers");
                }
            })
            XHR.send();
        })
    }

    // Mostrar/ocultar dropdown al hacer clic en la imagen
    const dropdown = document.querySelector('.dropdown-content');
        document.getElementById("more-options").addEventListener('click', function (event) {
            event.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });


        document.addEventListener('click', function () {
            dropdown.style.display = 'none';
        });

    // Obtener el modal y elementos interactivos
    const modal = document.getElementById('modal');
    const modalContent = document.querySelector('.modal-content');
    const modalTitle = document.getElementById('modal-title');
    const modalForm = document.getElementById('modal-form');
    const closeModal = document.querySelector('.close');

    // Función para mostrar el modal con contenido dinámico
    function openModal(title, inputType, placeholder) {
        modalTitle.textContent = title;

        // Limpiar formulario previo
        modalForm.innerHTML = '';

        // Crear input dinámico
        const input = document.createElement('input');
        input.type = inputType;
        input.name = 'modal-input';
        input.placeholder = placeholder;
        input.required = true;
        input.style.width = '50%';
        input.style.padding = '10px';
        input.style.marginTop = '15px';
        input.style.marginBottom = '15px';

        // Añadir el input al formulario
        modalForm.appendChild(input);

        // Añadir un botón de enviar
        const submitButton = document.createElement('button');
        submitButton.textContent = 'Guardar';
        submitButton.type = 'submit';
        submitButton.style.padding = '10px 20px';
        modalForm.appendChild(submitButton);

        // Mostrar el modal
        modal.style.display = 'block';
    }

    // Eventos para las opciones del menú desplegable
    document.getElementById('change-photo').addEventListener('click', function (event) {
        event.preventDefault();
        openModal('Cambiar foto de perfil', 'file', 'Selecciona una foto');
    });

    document.getElementById('change-name').addEventListener('click', function (event) {
        event.preventDefault();
        openModal('Cambiar nombre de perfil', 'text', 'Escribe tu nuevo nombre');
    });

    document.getElementById('change-description').addEventListener('click', function (event) {
        event.preventDefault();
        openModal('Cambiar descripción', 'text', 'Escribe tu nueva descripción');
    });

    // Cerrar modal
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Cerrar modal al hacer clic fuera de él
    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
