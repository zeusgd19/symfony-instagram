$(document).ready(function(){
    let stories;
    const userId = $('.story-main-content').attr('data-id');
    $.ajax({
        type: 'POST',
        url: '/stories',
        data: JSON.stringify({ userId: userId }),
        contentType: 'application/json',
        success: function(data){
            stories = data.stories;
            console.log(stories)
        }
    })
    console.log(stories);

    let currentIndex = 0;
    $(document).on('click','.next, .previous',function(e){
        console.log('Hola')
        e.preventDefault();
        let currentStory = stories[currentIndex].image;
        if(stories.length > 1){
            if($(this).hasClass('next')){
                currentIndex++;
                if(currentIndex > stories.length - 1) {
                    currentIndex = stories.length - 1;
                }
                currentStory = stories[currentIndex].image;
                $('.story-center').find('.story').find('img').attr('src',currentStory);
            }
            if($(this).hasClass('previous')){
                currentIndex--;
                if(currentIndex < 0){
                    currentIndex = 0;
                }
                currentStory = stories[currentIndex].image;
                $('.story-center').find('.story').find('img').attr('src',currentStory);
            }
        }
    })
})