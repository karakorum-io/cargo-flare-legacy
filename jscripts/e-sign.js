const $eSign = {

    URL: BASE_PATH + "application/ajax/signature.php",
    signature: null,

    typeName: ref => {
        $eSign.signature = ref.value.trim();
        document.getElementById('typedName').innerHTML = $eSign.signature;
    },

    sign: (ref, hash, entityId, type) => {

        $eSign.process(ref);

        if ($eSign.signature === null || $eSign.signature === "") {
            $engine.notify("Name cannot be left blank");
            $eSign.restoreButton(ref);
            return false;
        }

        if (!$('#esign-terms').is(':checked')) {
            $engine.notify("Please accept terms first");
            $eSign.restoreButton(ref);
            return false;
        }

        let data = {};

        // when type is order
        if (type === 'order') {
            data = {
                data: $eSign.signature,
                type: 'text',
                width: 400,
                height: 100,
                signType: "order",
                notes: $("#notes").val().trim(),
                hash: hash,
                id: entityId
            }
        }

        $eSign.processEsign(data);
    },

    signMobile: (ref, hash, entityId, type) => {

        $eSign.process(ref);

        if ($eSign.signature === null || $eSign.signature === "") {
            $engine.notify("Name cannot be left blank");
            $eSign.restoreButton(ref);
            return false;
        }

        if (!$('#esign-terms-mobile').is(':checked')) {
            $engine.notify("Please accept terms first");
            $eSign.restoreButton(ref);
            return false;
        }

        let data = {};

        // when type is order
        if (type === 'order') {
            data = {
                data: $eSign.signature,
                type: 'text',
                width: 400,
                height: 100,
                signType: "order",
                notes: $("#notes-mobile").val().trim(),
                hash: hash,
                id: entityId
            }
        }

        $eSign.processEsign(data);
    },

    signDispatch: (ref, dispatchId) => {
        $eSign.process(ref);

        if ($eSign.signature === null || $eSign.signature === "") {
            $engine.notify("Name cannot be left blank");
            $eSign.restoreButton(ref);
            return false;
        }

        if (!$('#esign-terms').is(':checked')) {
            $engine.notify("Please accept terms first");
            $eSign.restoreButton(ref);
            return false;
        }

        $engine.confirm("Are you sure, you want to accept?", action => {
            
            if (action === "confirmed") {
                let data = {
                    data: $eSign.signature,
                    type: 'text',
                    width: 400,
                    height: 100,
                    signType: "dispatch_new",
                    notes: $("#notes").val().trim(),
                    id: dispatchId
                }

                $eSign.processEsign(data);
            }

            $eSign.restoreButton(ref);
        });
    },

    signDispatchMobile: (ref, dispatchId) => {
        $eSign.process(ref);

        if ($eSign.signature === null || $eSign.signature === "") {
            $engine.notify("Name cannot be left blank");
            $eSign.restoreButton(ref);
            return false;
        }

        if (!$('#esign-terms-mobile').is(':checked')) {
            $engine.notify("Please accept terms first");
            $eSign.restoreButton(ref);
            return false;
        }

        $engine.confirm("Are you sure, you want to accept?", action => {
            
            if (action === "confirmed") {
                let data = {
                    data: $eSign.signature,
                    type: 'text',
                    width: 400,
                    height: 100,
                    signType: "dispatch_new",
                    notes: $("#notes-mobile").val().trim(),
                    id: dispatchId
                }

                $eSign.processEsign(data);
            }

            $eSign.restoreButton(ref);
        });
        
    },

    signDispatchReject: (ref, dispatchId) => {
        $engine.confirm("Are you sure, you want to reject?", action => {
            
            if (action === "confirmed") {
                let data = {
                    action: "reject",
                    id: dispatchId
                }

                $engine.asyncPost(BASE_PATH + "application/ajax/dispatch.php", data, (response) => {
                    if (response.success) {
                        location.href = BASE_PATH;
                    } else {
                        $engine.notify("Something went wrong contact support.");
                    }
                });
            }
        });
    },

    processEsign: data => {
        $engine.asyncPost($eSign.URL + "?type=text&width=" + data.width + "&height=" + data.height, data, (response) => {
            if (response.success) {
                window.open(response.url, "_self");
            } else {
                $engine.notify("Something went wrong contact support.");
                $eSign.restoreButton(document.getElementById('eSignNow'));
            }
        });
    },

    restoreButton: (ref) => {
        setTimeout(() => {
            ref.innerHTML = "Click To E-Sign";
            ref.removeAttribute("disabled");
        }, 500);
    },

    process: (ref) => {
        ref.innerHTML = "Processing ... ";
        ref.setAttribute("disabled", true);
    }
}