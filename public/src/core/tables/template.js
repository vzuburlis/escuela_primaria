gtableCommand.testT = {
    fa: "envelope",
    label: "Test",
    permission: "update",
    fn: function (table,irow) {
        g.post('testTemplate/' + irow, '', function () {
            alert('Email sent')})
    }
}

gtableCommand.previewT = {
    fa: "eye",
    label: "Preview",
    permission: "update",
    fn: function (table,irow) {
        window.location.href = 'previewTemplate/' + irow
    }
}
