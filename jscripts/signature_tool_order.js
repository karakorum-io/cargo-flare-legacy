/*
    Signature Tool 1.0
    You should edit function sg_apply to get signature url
*/
/* CONFIG */
var sg_save_url = BASE_PATH + "application/ajax/signature.php";
var canvas_width = 400;
var canvas_height = 100;
var signature_type = "dispatch";
var sg_id = 0;

/* DO NOT TOUCH */
var MIN_DIST = 3;
var dom_paper = null;
var paper = null;
var sg_is_enabled = false;
var sg_is_pressed = false;
var h_draw_finish = 0;
var path = [];
var path_el = null;
var curr_color = '000000';
var res_path = [];
var res_path_els = [];
var sign_url = null;

String.prototype.format = function() {
    var res = this;
    for (var i = 0; i < arguments.length; i++) {
        res = res.replace(new RegExp('\\{' + i + '\\}', 'g'), String(arguments[i]));
    }
    return res;
}

function sg_cancel_event(ev) {
    ev.returnValue = false;
    ev.cancelBubble = true;
    if (ev.stopPropagation) { ev.stopPropagation(); }
}

function sg_ldist(x, y) {
    if (!path.length) {
        return 0;
    }
    var lx = path[path.length - 1].x;
    var ly = path[path.length - 1].y;
    return Math.sqrt((x - lx) * (x - lx) + (y - ly) * (y - ly));
}

function sg_update_path() {
    if (!path.length) {
        return;
    }
    var path_str = '';
    if (path.length == 1) {
        path_str = 'M{0},{1} L{2},{1}'.format(path[0].x, path[0].y, path[0].x + 1);
    } else {
        path_str = 'M{0},{1}'.format(path[0].x, path[0].y);
        for (var i = 1; i < path.length; i++) path_str += ' L{0},{1}'.format(path[i].x, path[i].y);
    }
    path_el.attr({ 'path': path_str });
}

function sg_mouse_coords(ev) {
    var mx = 0;
    var my = 0;
    if (document.all && !window.opera) {
        mx = ev.clientX - dom_paper.offsetLeft - dom_paper.offsetParent.offsetLeft;
        my = ev.clientY - dom_paper.offsetTop - dom_paper.offsetParent.offsetTop;
    } else {
        mx = (typeof(ev.offsetX) == 'undefined' ? ev.layerX : ev.offsetX);
        my = (typeof(ev.offsetY) == 'undefined' ? ev.layerY : ev.offsetY);
    }
    return { 'x': mx, 'y': my };
}

function sg_begin_draw(ev) {
    if (!sg_is_enabled) return true;
    if (!ev) ev = event;
    if (h_draw_finish) {
        clearTimeout(h_draw_finish);
        h_draw_finish = 0;
    }
    if (!sg_is_pressed) {
        var m = sg_mouse_coords(ev);
        sg_is_pressed = true;
        path_el.toFront();
        path = [{ 'x': m.x, 'y': m.y }];
        sg_update_path();
    }
    sg_cancel_event(ev);
    return false;
}

function sg_draw_it(ev) {
    if (!sg_is_enabled) return true;
    if (!ev) ev = event;
    if (sg_is_pressed) {
        var m = sg_mouse_coords(ev);
        if (sg_ldist(m.x, m.y) >= MIN_DIST) {
            path.push({ 'x': m.x, 'y': m.y });
            sg_update_path();
        }
    }
    sg_cancel_event(ev);
    return false;
}

function sg_end_draw(ev) {
    if (!sg_is_enabled) return true;
    if (!ev) ev = event;
    if (sg_is_pressed) {
        sg_is_pressed = false;
        var m = sg_mouse_coords(ev);
        if (sg_ldist(m.x, m.y) >= MIN_DIST) {
            path.push({ 'x': m.x, 'y': m.y });
        }
        sg_store_path();
        sg_cancel_event(ev);
        return false;
    }
    return true;
}

function sg_do_clear() {
    paper.clear();
    path_el = paper.path();
    path_el.attr({
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round'
    });
}

function sg_clear() {
    if (!sg_is_enabled) return;
    res_path = [];
    res_path_els = [];
    sg_do_clear();
}

function sg_draw_res_item(item) {
    var path_str = '';
    if (item.length == 4) {
        path_str = 'M{0},{1} L{2},{1}'.format(item[2], item[3], item[2] + 1);
    } else {
        path_str = 'M{0},{1}'.format(item[2], item[3], item[2]);
        for (var i = 4; i < item.length; i += 2) path_str += ' L{0},{1}'.format(item[i], item[i + 1]);
    }
    var ret_el = paper.path(path_str).attr({
        'stroke': '#' + item[0],
        'stroke-width': item[1],
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round'
    });
    return ret_el;
}

function sg_undo() {
    if (!sg_is_enabled) return;
    if (!res_path.length) return;
    res_path = res_path.slice(0, res_path.length - 1);
    res_path_els[res_path_els.length - 1].remove();
    res_path_els = res_path_els.slice(0, res_path_els.length - 1);
}

function sg_store_path() {
    if (!sg_is_enabled) return;
    path_el.attr({ 'path': 'M0,0' });
    if (!path.length) return;
    var res_item = [curr_color, 2];
    for (var i = 0; i < path.length; i++) {
        res_item.push(path[i].x);
        res_item.push(path[i].y);
    }
    res_path.push(res_item);
    res_path_els.push(sg_draw_res_item(res_item));
}

function sg_save_draw() {
    if (!sg_is_enabled) return;
    if (!res_path.length) return;
    var data = new Object();
    for (i in res_path) {
        data['data[' + i + '][]'] = res_path[i];
    }
    data['type'] = 'hand';
    data['width'] = canvas_width;
    data['height'] = canvas_height;
    data['signType'] = signature_type;
    data['id'] = sg_id;
    $.ajax({
        type: "POST",
        url: sg_save_url,
        dataType: 'json',
        data: data,
        success: signatureSuccess,
        complete: function(result) {}
    });
}

function sg_save_write() {
    var val = $.trim($("#sign_name").val());
    if (val == "") return;
    $.ajax({
        type: "POST",
        url: sg_save_url + "?type=text&width=" + canvas_width + "&height=" + canvas_height,
        dataType: 'json',
        data: {
            data: val,
            type: 'text',
            width: canvas_width,
            height: canvas_height,
            signType: signature_type,
            id: sg_id
        },
        success: signatureSuccess,
        complete: function(result) {}
    });
}

function signatureSuccess(result) {
    if (result.url != undefined) {
        sign_url = result.url;
        $("#signature-apply").removeClass('disabled');
        $("#sign-result img").attr("src", result.url);
        $("#paper").hide();
        $("#sign-result").show();
        $(".type_selector, .sign-controls").remove();
        if (signature_type == 'dispatch') {
            document.location.href = document.location.href;
        } else {
            $('#sign_button').remove();
            alert("Thank you! Your order has been signed and will be processed.");
        }
    }
}

function signatureReset() {
    $("#sign-result").hide();
    sg_clear();
    $("#signature-apply").addClass("disabled");
    sign_url = null;
    $("#paper").show();
    if ($("input[name='sign_type']:checked").val() == 'text') {
        $("#sign_draw_controls").hide();
        $("#sign_write_controls").show();
        sg_is_enabled = false;
    } else {
        $("#sign_write_controls").hide();
        $("#sign_draw_controls").show();
        sg_is_enabled = true;
    }
}

function changeSignType(val) {
    switch (val) {
        case 'text':
            $("#sign_draw_controls").hide();
            $("#sign_write_controls").show();
            $("#sign-result").hide();
            sg_clear();
            $("#paper").show();
            sg_is_enabled = false;
            break;
        case 'draw':
            $("#sign_write_controls").hide();
            $("#sign_draw_controls").show();
            $("#sign-result").hide();
            sg_clear();
            $("#paper").show();
            sg_is_enabled = true;
            break;
    }
}

function sg_apply() {
    // Put your code here
    console.log("Signature URL: " + sign_url);
}

$(document).ready(function() {
    $("#paper").width(canvas_width);
    $("#paper").height(canvas_height);
    $("#sign-result").width(canvas_width);
    $("#sign-result").height(canvas_height);
    paper = new Raphael('paper', canvas_width, canvas_height);
    path_el = paper.path();
    path_el.attr({
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round'
    });

    dom_paper = document.getElementById('paper');
    dom_paper.onmousedown = sg_begin_draw;
    dom_paper.onmousemove = sg_draw_it;
    document.onmouseup = sg_end_draw;

    $("#signature-undo").click(sg_undo);
    $("#signature-clear").click(sg_clear);
    $("#signature-save-draw").click(sg_save_draw);
    $("#signature-save-text").click(sg_save_write);
    $("#signature-apply").click(function() {
        if ($(this).hasClass("disabled")) return;
        sg_apply();
    });
    $("#signature-reset").click(function() {
        signatureReset();
    });
    $("input[name='sign_type']").click(function() {
        $("#signature-apply").addClass("disabled");
        sign_url = null;
        changeSignType($("input[name='sign_type']:checked").val());
    });
    $('#signature_tool').dialog({
        'title': 'Electronic Signature',
        'width': 430,
        'height': 220,
        'autoOpen': false,
        'resizable': false,
        'draggable': true
    });
    $('#signature_tool button').button();
});