document.addEventListener('DOMContentLoaded',function (){
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
        const moreOptions = document.getElementById("more-options");
        if (moreOptions){
            moreOptions.addEventListener('click', function (event) {
                event.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });
        }

        document.addEventListener('click', function () {
            dropdown.style.display = 'none';
        });

        // Obtener el modal y elementos interactivos
        const modal = document.getElementById('modal');
        const modalContent = document.querySelector('.modal-content');
        const modalTitle = document.getElementById('modal-title');
        const closeModal = document.querySelector('.close');

        // Función para mostrar el modal con contenido dinámico
        function openModal(title, inputType, placeholder) {
            form = document.createElement("form");

            modalTitle.textContent = title;

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

            // Añadir un botón de enviar
            const submitButton = document.createElement('button');
            submitButton.textContent = 'Guardar';
            submitButton.type = 'submit';
            submitButton.style.padding = '10px 20px';

            form.appendChild(input);
            form.appendChild(submitButton);

            form.addEventListener("submit", function (event) {
                event.preventDefault();
                XHR = new XMLHttpRequest();
                if (title.includes("nombre")) {
                    XHR.open("POST", `/profile/change-username/${input.value}`);

                } else {
                    XHR.open("POST", `/profile/change-description/${input.value}`)
                }

                XHR.addEventListener("readystatechange", function () {
                    if (XHR.readyState !== 4) return;
                    if (XHR.status === 200) {
                        console.log("hola");
                        window.location.reload();
                    }
                })

                XHR.send(null);
            });

            modalContent.appendChild(form);

            // Mostrar el modal
            modal.style.display = 'block';
        }
    $('#profile-send-message').on('click',function(){
        let photo = $('#profile').find('#narutoProfilePic').attr('src');
        let username = $('#profile').find('#profile-name').text();
        let userId = $('#profile').find('#profile-name').attr('data-id');
        let senderId = $('#profile').find('#profile-name').attr('data-sender-id')
        window.location.href = '/directMessages';

        sessionStorage.setItem('selectedUser', JSON.stringify({ photo, username, userId, senderId }));
    })

    const changePhoto = document.getElementById('change-photo');
    const changeName = document.getElementById('change-name');
    const changeDescription = document.getElementById('change-description');

    if(changePhoto && changeName && changeDescription) {
        changePhoto.addEventListener('click', function (event) {
            event.preventDefault();
            modal.style.display = "block";
        });

        changeName.addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById("formulario").classList.add("hide");
            openModal('Cambiar nombre de perfil', 'text', 'Escribe tu nuevo nombre');
        });

        changeDescription.addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById("formulario").classList.add("hide");
            openModal('Cambiar descripción', 'text', 'Escribe tu nueva descripción');
        });


        // Cerrar modal
        closeModal.addEventListener('click', function () {
            modalContent.removeChild(modalContent.lastChild);
            modal.style.display = 'none';
        });

        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
