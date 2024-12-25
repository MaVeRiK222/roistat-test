let roistatFormHanlderfunction = function () {

    const validateFunctions = {
        'name': validateName,
        'email': validateEmail,
        'phone': validatePhone,
        'price': validatePrice,
    }

    $(document).on('submit', 'form.form', function (event) {
        event.preventDefault();
        let data = $(this).serializeArray();
        if (isDataValid(data)) {
            sendData(data);
        }
    });

    setTimeout(() => {
        $('form.form input[hidden]').attr({ 'value': 1 });
    }, 1000 * 30);

    function isDataValid(data) {
        errMessage = "";
        for (let i in data) {
            if (validateFunctions[data[i].name] != undefined) {
                let validateResponse = validateFunctions[data[i].name](data[i]);
                if (validateResponse.errMessage.length != 0) errMessage += validateResponse.errMessage + '\n';
            }

        }
        if (errMessage.length == 0) return true;
        alert(errMessage);
        return false;
    }

    function sendData(data) {
        $.ajax({
            url: "/php/webhook.php",
            type: "POST",
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            data: data,
            success: function (response) {
                alert("Данные отправлены");
            },
            error: function (xhr, status, error) {
                alert("Произошла ошибка " + error);
            }
        });
    }

    function validateName(data) {
        let errMessage = '';
        let flag = true;
        //Предположим что в имени могут быть только буквы и пробел
        let filtredValue = data.value.replace(/[^a-zA-Zа-яА-Я ]/g, '');

        if (filtredValue == 0 || filtredValue.length != data.value.length) {
            flag = false
            errMessage = 'Некорректное имя';
        }
        else { data.value = filtredValue };
        return { isValid: flag, errMessage: errMessage };
    }

    function validatePhone(data) {
        let errMessage = '';
        filtredValue = data.value.replace(/[^0-9+() -]/g, '');
        // Предположим что номера только российские
        if (filtredValue.length == 0 || filtredValue.length != 11 || filtredValue.length != data.value.length) {
            flag = false;
            errMessage = 'Некорректный номер телефона';
        }
        else data.value = filtredValue;
        return { isValid: flag, errMessage: errMessage };
    }

    function validateEmail(data) {
        let errMessage = '';
        const regex = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
        flag = regex.test(data.value);
        if (!flag) {
            errMessage = 'Некорректная почта';
        }
        return { isValid: flag, errMessage: errMessage };
    }

    function validatePrice(data) {
        let errMessage = '';
        let flag = true;
        let filtredValue = data.value.replace(/[^0-9]/g, '');
        if (data.value.length != filtredValue.length) {
            flag = false;
            errMessage = 'Некорректная цена';
        }
        return { isValid: flag, errMessage: errMessage };
    }
}();
