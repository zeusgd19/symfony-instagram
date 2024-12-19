$(document).ready(function() {
    const firstUserInList = $('.message-user-item').find('.user-item')[0]
    const allUsers = $('.message-user-item').find('.user-item')
    const messages = document.getElementsByClassName('messagesUl')[0];
    sessionStorage.setItem('data-sender-id',$(firstUserInList).attr('data-sender-id'));
    sessionStorage.setItem('data-id',$(firstUserInList).attr('data-id'));
    sessionStorage.setItem('photo-user',$(firstUserInList).find('img').attr('src'));

    const allUsersId = [];

    allUsers.each(function(){
        allUsersId.push($(this).attr("data-id"))
    })

    if(firstUserInList) {
        const userId = firstUserInList.getAttribute('data-id')
        $.ajax({
            type: "GET",
            url: `/messages/${userId}`,
            dataType: 'json',
            beforeSend: function (){
                $('.messagesUl').html('<div class="loading-container"><img src="img/loading-buffer.gif" width="30" height="30"></div>');
            },
            success: function (response) {
                $('.messagesUl').html(response.messages)
                messages.scrollTop = messages.scrollHeight;
            }
        });
    }
    let user = sessionStorage.getItem('selectedUser');
    if (user) {
        user = JSON.parse(user);
        // Añadir el usuario al contenedor de mensajes
        $('.message-user-item').append(`
            <div class="user-item" data-id="${user.userId}" data-sender-id="${user.senderId}">
                <img src="${user.photo}" alt="Photo" />
                <div>
                    <p id="usernameMessageItem">${user.username}</p>
                </div>
            </div>
        `);

        /*
        fetch('/directMessages', {
            method: 'POST',
            body: JSON.stringify({ receiverId: user.userId }),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (!response.ok) {
                throw new Error('Error al enviar el mensaje');
            }
            return response.json();
        }).then(data => {
            console.log('Mensaje enviado correctamente:', data);
        }).catch(error => {
            console.error('Error:', error);
        });

         */
        // Opcional: Limpiar los datos del sessionStorage
        sessionStorage.removeItem('selectedUser');
    }

    $(document).on('click','.user-item',function(){

        sessionStorage.removeItem('data-sender-id');
        sessionStorage.removeItem('data-id');
        sessionStorage.removeItem('photo-user');
        let userId = $(this).attr('data-id');
        let senderId = $(this).attr('data-sender-id')
        let photo = $(this).find('img').attr('src');
        $('.message-item-user-info').find('img').remove();
        $('.message-item-user-info').find('p').remove();
        $('.message').remove();
        $('.message-item-user-info').attr('data-id',userId);
        $('.message-item-user-info').attr('data-sender-id',senderId);
        sessionStorage.setItem('data-new-sender-id',senderId);
        sessionStorage.setItem('data-new-id',userId);
        sessionStorage.setItem('new-photo-user',photo);
        $('.message-item-user-info').append(
            `
            <img src="${$(this).find('img').attr('src')}"/>
            <p>${$(this).find('#usernameMessageItem').text()}</p>
            `
        );

        $.ajax({
            type: "GET",
            url: `/messages/${userId}`,
            dataType: 'json',
            beforeSend: function (){
                $('.messagesUl').html('<div class="loading-container"><img src="img/loading-buffer.gif" width="30" height="30"></div>');
            },
            success: function (response) {
                $('.messagesUl').html(response.messages)
                messages.scrollTop = messages.scrollHeight;
            }
        });
    });

    $('.send-messages').find('img').on('click', function () {
        let input = $(this).parent().find('input');
        let value = $(this).parent().find('input').val();
        let receiverId = $(this).parent().parent().find('.message-item-user-info').attr('data-id');

        $('.message-item').find('ul').append(
            `
                <li class="message owner">
                    <p>${value}</p>
                </li>
                `
        );
        $(input).val("");
        messages.scrollTop = messages.scrollHeight;
        $.ajax({
            type: "POST",
            url: "/message/new",
            data: JSON.stringify({ receiverId: receiverId, content: value }),
            contentType: "application/json",
            success: function (response) {
            }
        });
    });

    const supabaseUrl = 'https://fnofdrpcrthobxniqwkw.supabase.co';

    const cliente = supabase.createClient(supabaseUrl, supabaseKey);

    let lastSenderId = -1;
    // Configuración para Realtime
    cliente
        .channel('mensajes')
        .on(
            'postgres_changes',
            { event: 'insert', schema: 'public', table: 'message' },
            async (payload) => {
                let {sender_id, receiver_id, content} = payload.new;
                let logedId = sessionStorage.getItem('data-sender-id');
                let seeingReceiverId =  $('.message-item-user-info').attr('data-id')
                let newPhoto = sessionStorage.getItem('new-photo-user');
                let inList = false;
                for(let id of allUsersId){
                    if(sender_id == id){
                        inList = true;
                        break;
                    }
                }
                if( !inList && logedId != sender_id){
                    const { data: user, error } = await cliente
                        .from('user_postgres')
                        .select('id, username, photo')
                        .eq('id', sender_id)
                        .single();

                    if (error) {
                        console.error('Error al obtener datos del usuario:', error);
                        return;
                    }

                    $('.message-user-item').append(`
                        <div class="user-item" data-id="${user.id}" data-sender-id="${receiver_id}">
                        <img src="${user.photo}" alt="Photo" />
                        <div>
                            <p id="usernameMessageItem">${user.username}</p>
                        </div>
                    </div>
                `);
                    sessionStorage.setItem('data-new-id',user.id);
                    sessionStorage.setItem('new-photo-user',user.photo);
                    lastSenderId = sender_id;
                }
                if(receiver_id == logedId){
                    if(sender_id == seeingReceiverId) {
                        $('.message-item').find('ul').append(
                            `
                            <li class="message other">
                            <img src="${newPhoto}">
                            <p>${content}</p>
                            </li>
                           `
                        )
                        messages.scrollTop = messages.scrollHeight;
                    }
                }

            })
        .subscribe();
})