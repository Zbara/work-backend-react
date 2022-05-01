import {Ajax} from "./Ajax";
import {toast} from "./src/toast"
import {alert} from "./src/alert";
import {FormValidate} from "./formValidate";
import {butloading, generateTableRow} from "./src/libs";

class Admin extends Ajax {
    static login = '/admin/edit/save/login';
    static password = '/admin/edit/save/pass';

    constructor() {
        const adminLogin = document.querySelector('#adminLogin')
        const adminPass = document.querySelector('#adminPass')

        if (adminLogin) {
            adminLogin.addEventListener('submit', (event) => {
                event.preventDefault();

                Admin.EditLogin(event);
            });
            FormValidate.isError(adminLogin);
        }

        if (adminPass) {
            adminPass.addEventListener('submit', (event) => {
                event.preventDefault();

                Admin.EditPass(event);
            });
            FormValidate.isError(adminPass);
        }
        super();
    }

    static EditLogin(event) {
        let formData = new FormData(event.target);
        if (FormValidate.isValid(formData)) {
            this.post(this.login, formData, {
                onDone: function (result) {
                    alert(result.messages, 'success')
                },
                onFail: function (error) {
                    FormValidate.getErrorForm(error.messages, event.target);
                },
                showProgress: function () {
                    butloading(event.submitter, true)
                },
                hideProgress: function () {
                    butloading(event.submitter, false, 'Изменить')
                }
            });
        }
    }

    static EditPass(event) {
        let formData = new FormData(event.target);
        if (FormValidate.isValid(formData)) {
            this.post(this.password, formData, {
                onDone: function (result) {
                    alert(result.messages, 'success')
                },
                onFail: function (error) {
                    FormValidate.getErrorForm(error.messages, event.target);
                },
                showProgress: function () {
                    butloading(event.submitter, true)
                },
                hideProgress: function () {
                    butloading(event.submitter, false, 'Изменить')
                }
            });
        }
    }
}

new Admin();
