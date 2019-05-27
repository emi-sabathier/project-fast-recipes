class Comments {
    constructor(baseurl) {
        this.baseUrl = baseurl;
        $('.report-comment').on('click', this.reportComment.bind(this));
        $('.post-comment').on('click', this.postComment.bind(this));
        $('.reset-report').on('click', this.resetReport.bind(this));
        $('.delete-comment').on('click', this.deleteComment.bind(this));
    }

    reportComment(e) {
        let idComment = $(e.target).attr('data-id');
        $.post(this.baseUrl + '/reportcomment', {commentId: idComment}, (response) => {
            if (response === 'success') {
                const divElt = $('<div id="report-popup">').appendTo('#comments');
                divElt.css(
                    {
                        "position": "absolute",
                        "top": "50%",
                        "padding": "1rem",
                        "font-weight": "bold",
                        "text-align": "center",
                        "left": "40%",
                        "background-color": "#fff",
                        "border": "2px solid #000"
                    });
                divElt.text('Signalement envoyé');
                +
                    setTimeout(() => {
                        $('#report-popup').remove();
                    }, 2000);
            } else {
                console.log('Une erreur est survenue');
            }
        }, 'JSON');
    }

    postComment(e) {
        e.preventDefault();
        let id = $('.post-comment').attr('data-id');
        let commentVal = $('textarea#comment').val();
        const messageElt = $('<div id="message-comment">').appendTo('#comment-form');

        $.post(this.baseUrl + '/recipes/postcomment', {recipeId: id, comment: commentVal}, (response) => {

            if (response.status === 'success') {
                $('section#comments').prepend([
                    $('<div>', {'data-id': response.comment.commentId}).append([
                        $('<strong>').text(response.comment.nickname),
                        $('<span>').text(' Le ' + response.comment.date + ' '),
                        $('<button>', {
                            'data-id': response.comment.commentId,
                            'class': 'report-comment btn btn-danger p-1',
                            'text': 'Signaler'
                        }).on('click', this.reportComment.bind(this)),
                        $('<p>').text(response.comment.content)
                    ])
                ]);

                $('#message-comment').text('Message envoyé');
                setTimeout(() => {
                    $('#message-comment').remove();
                }, 2000);
            } else {
                $('#message-comment').text('Commentaire invalide');
                setTimeout(() => {
                    $('#message-comment').remove();
                }, 2000);
            }
        }, 'JSON');
    }
    resetReport(e) {
        let idComment = $(e.target).attr('data-id');
        $.post(this.baseUrl + '/admin/resetreport', {commentId: idComment}, (response) => {
            if (response === 'success') {
                $(e.target).parent().parent().remove();
            } else {
                console.log('Une erreur est survenue');
            }
        }, 'JSON');
    }
    deleteComment(e){
        let idComment = $(e.target).attr('data-id');
        $.post(this.baseUrl + '/admin/deletecomment', {commentId: idComment}, (response) => {
            if(response === 'success'){
                $(e.target).parent().parent().remove();
            } else {
                console.log('Une erreur est survenue');
            }
        }, 'JSON');
    }
}