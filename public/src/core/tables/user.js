
gtableFieldDisplay.photo = function (rv) {
    if (rv.photo == null || rv.photo.length == 0) {
        let letter = ""
        if (rv.username) {
            letter = rv.username.toUpperCase()[0];
        }
        let color = ['red', 'lightseagreen', 'green', 'hotpink', 'darkorange', 'brown', 'blueviolet'][rv.id % 7]
        return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%;' +
        'width:40px;height:40px;background:' + color + '; color: white;\
    font-size: 24px; padding:6px; text-align: center; font-family: Arial;">' + letter + '</div>'
    }
    photo = rv.photo
    if (!photo.startsWith('https:') && !photo.startsWith('http:')) {
        photo = 'lzld/thumb?src=' + photo + '&media_thumb=60'
    }
    return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%; ' +
    'width:40px;height:40px;background:url(' + photo + '); ' +
    'background-position: center; background-size:cover;"></div>'
}

gtableTool.invite_user = {
    fa: 'envelope',
    label: g.tr('Invite'),
    permission: 'create',
    fn: function (table) {
        if (typeof table.filters == 'undefined') {
            table.filters = ''
            href = 'cm/edit_form/' + table.name + '?list=invite&callback=g_form_popup_invite' + table.filters;
        }
        g.get(href, function (data) {
            g.dialog({title:g.tr('New Registry'), class:'lightscreen large',body:data,type:'modal',buttons:'popup_invite'})
            formId = '#' + table.name + '-edit-item-form'
            textarea = g('#gila-popup textarea').first()
            if (!textarea || !textarea.innerHTML.includes('{{')) {
                edit_popup_app = new Vue({
                    el: formId,
                    data: {id:0}
                })
            }
            transformClassComponents()
            g(formId + ' input').all[1].focus()
        })
    }
}

gtableCommand.select_groups = {
    fa: 'link',
    label: 'Joins',
    permission: 'update',
    fn: function (table,irow) {
        join_table_dialog(table,'usergroup',irow,'small') },
}

function join_table_dialog(table, joins, irow, cl='large')
{
    href = 'cm/join_table/' + table.name + '?field=' + joins + '&id=' + irow + '&callback=g_form_popup_list_update';
    g.get(href,function (response) {
        g.modal({class:cl,body:response,type:'modal',buttons:'popup_update'})
        formId = '#' + table.name + '-edit-item-form'
        textarea = g('#gila-popup textarea').first()
        formValues = []
        if (typeof g(formId).all[0].dataset.values != 'undefined') {
            formValues = JSON.parse(g(formId).all[0].dataset.values)
        }
        if (!textarea || !textarea.innerHTML.includes('{{')) {
            edit_popup_app = new Vue({
                el: formId,
                data: {id:irow,formValue:formValues}
            })
        }
        transformClassComponents()
        g(formId + ' input').all[1].focus()
    },function (data) {
        data = JSON.parse(data)
        g.alert(data.error)
    })
}

function g_form_popup_list_update()
{
    form = g('.gila-popup form').last()
    data = new FormData(form);
    if (g_form_popup_selected_field !== null) {
        tmp = data.get(g_form_popup_selected_field)
        data = new FormData()
        data.append(g_form_popup_selected_field, tmp)
        g_form_popup_selected_field = null
    }
    t = form.getAttribute('data-table')
    id = form.getAttribute('data-id')
    field = form.getAttribute('data-field')
    for (i = 0; i < rootVueGTables.length; i++) {
        if (rootVueGTables[i].name == t) {
            _this = rootVueGTables[i]
        }
    }

    url = 'cm/join_table/' + t + '?id=' + id + '&field=' + field

    g.loader()
    g.ajax({method:'post',url:url,data:data,fn:function (data) {
        g.loader('false')
        if (g_form_popup_update_reload) {
            location.reload()
        }
        data = JSON.parse(data)
        if (data.error) {
            g.alert(data.error, 'error')
            return
        }
        _this.update_rows(data)
        if (_this.layout == 'kanban') {
            g.loader()
            _this.load_page()
        }
    }})

    g.closeModal();
}
