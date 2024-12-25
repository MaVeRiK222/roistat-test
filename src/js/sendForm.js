let roistatFormHanlderfunction = function () {

    const validateFunctions = {
        'name': validateName,
        'email': validateEmail,
        'phone': validatePhone,
        'price': validatePrice,
    }
    // alert('Подключились');
    const $form = $('form.form');
    console.log($form);


    $(document).on('submit', 'form.form', function (event) {
        event.preventDefault();
        let data = $(this).serializeArray();
        console.log(data);
        if (isDataValid(data)) {
            sendData(data);
        }
        else { console.log('Данные не прошли валидацию'); }
        console.log('После обработки');
        console.log(data);
    });

    setTimeout(() => {
        $('form.form input[hidden]').attr({ 'value': 1 });
    }, 1000 * 10);

    function isDataValid(data) {
        errMessage = "";
        for (let i in data) {
            console.log(i);
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
        console.log('validateName');
        console.log(data);
        let flag = true;
        //Предположим что в имени могут быть только буквы и пробел
        let filtredValue = data.value.replace(/[^a-zA-Zа-яА-Я ]/g, '');
        console.log(filtredValue);
        if (filtredValue == 0 || filtredValue.length != data.value.length) {
            flag = false
            errMessage = 'Некорректное имя';
        }
        else { data.value = filtredValue };
        console.log(data);
        return { isValid: flag, errMessage: errMessage };
    }

    function validatePhone(data) {
        let errMessage = '';
        console.log('validatePhone');
        console.log(data);
        filtredValue = data.value.replace(/[^0-9+() -]/g, '');
        console.log(filtredValue);
        // Предположим что номера только российские
        if (filtredValue.length == 0 || filtredValue.length != 11 || filtredValue.length != data.value.length) {
            flag = false;
            errMessage = 'Некорректный номер телефона';
        }
        else data.value = filtredValue;
        console.log(data);
        return { isValid: flag, errMessage: errMessage };
    }

    function validateEmail(data) {
        let errMessage = '';
        console.log('validateEmail');
        const regex = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
        flag = regex.test(data.value);
        if (!flag) {
            errMessage = 'Некорректная почта';
        }
        console.log(data);
        return { isValid: flag, errMessage: errMessage };
    }

    function validatePrice(data) {
        let errMessage = '';
        let flag = true;
        console.log('validatePrice');
        console.log(data);
        let filtredValue = data.value.replace(/[^0-9]/g, '');
        console.log(filtredValue);
        if (data.value.length != filtredValue.length) {
            flag = false;
            errMessage = 'Некорректная цена';
        }
        console.log(data);
        return { isValid: flag, errMessage: errMessage };
    }
}();
