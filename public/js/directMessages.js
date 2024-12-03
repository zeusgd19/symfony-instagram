$(document).ready(function() {
    const firstUserInList = $('.message-user-item').find('.user-item')[0]
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

    $('.user-item').on('click',function(){

        let userId = $(this).attr('data-id');
        let senderId = $(this).attr('data-sender-id')
        $('.message-item-user-info').find('img').remove();
        $('.message-item-user-info').find('p').remove();
        $('.message').remove();
        $('.message-item-user-info').attr('data-id',userId);
        $('.message-item-user-info').attr('data-sender-id',senderId);
        sessionStorage.setItem('data-sender-id',senderId);
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
            }
        });
    });
    function stringToAscii(str) {
        return Array.from(str).map(char => char.charCodeAt(0));
    }

    $('.send-messages').find('img').on('click', function () {
        let input = $(this).parent().find('input');
        let value = $(this).parent().find('input').val();
        let receiverId = $(this).parent().parent().find('.message-item-user-info').attr('data-id');

        $.ajax({
            type: "POST",
            url: "/message/new",
            data: JSON.stringify({ receiverId: receiverId, content: value }),
            contentType: "application/json",
            success: function (response) {
                $('.message-item').find('ul').append(
                    `
                <li class="message owner">
                    <p>${response.message}</p>
                </li>
                `
                );
                $(input).val("");
            }
        });
    });

    const supabaseUrl = 'https://fnofdrpcrthobxniqwkw.supabase.co';

    const cliente = supabase.createClient(supabaseUrl, supabaseKey);

    // Configuración para Realtime
    cliente
        .channel('mensajes')
        .on(
            'postgres_changes',
            { event: 'insert', schema: 'public', table: 'message' },
            (payload) => {
                let {sender_id, receiver_id, content} = payload.new;
                let logedId = sessionStorage.getItem('data-sender-id');

                if(receiver_id == logedId){
                    $('.message-item').find('ul').append(
                        `
                            <li class="message other">
                            <p>${content}</p>
                            </li>
                           `
                    )
                }

            })
        .subscribe();
})