
gtableFieldDisplay.title = function (rv) {
    if (typeof rv.publish != 'undefined' && rv.publish != 1) {
        return rv.title;
    }
    la = ''
    if (rv.language && rv.language !== '') {
        la = rv.language + '/'
        return '<a target="_blank" href="' + la + rv.slug + '">' + rv.title + '</a>'
    }
}

gtableCommand.page_seo = {
    fa: 'search',
    label: 'SEO',
    permission: 'update',
    fn: function (table,irow) {
        gtableCommand.edit_side.fn(table,irow)
    }
}

gtableTool.new_item = {
    fa: 'plus', label: _e('Create'),
    permission: 'create',
    fn: function (table) {
        window.location.href = 'blocks/contentNew/' + table.name
    }
}
