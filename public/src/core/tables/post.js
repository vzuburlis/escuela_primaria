
gtableFieldDisplay.title = function (rv) {
    if (rv.publish != 1) {
        return rv.title;
    }
    la = ''
    if (rv.language && rv.language !== '') {
        la = rv.language + '/'
        return '<a target="_blank" href="' + la + 'blog/' + rv.id + '/' + rv.slug + '">' + rv.title + '</a>'
    }
}

