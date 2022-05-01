import {Ajax} from "./Ajax";
import {toast} from "./src/toast"
import {alert} from "./src/alert";
import {FormValidate} from "./formValidate";
import {butloading, generateTableRow} from "./src/libs";

class Client extends Ajax {
    static addApi = '/admin/users';
    static deleteApi = '/admin/users/remove';

    constructor() {
        const usersForm = document.querySelector('#usersForm')

        if (usersForm) {
            usersForm.addEventListener('submit', (event) => {
                event.preventDefault();

                Client.created(event);
            });
            FormValidate.isError(usersForm);
        }

        Client.deleteButton();

        super();
    }

    static deleteButton(){
        const deleteBtn = document.querySelectorAll('#delete-users');

        if (deleteBtn) {
            deleteBtn.forEach(function (item) {
                item.addEventListener("click", function () {
                    Client.delete(this.parentElement.parentNode)
                });
            });
        }
    }


    static delete(el) {
        this.post(this.deleteApi, this.formData({
            id: el.dataset.userId ?? 0
        }), {
            onDone: function (result) {
                if (result.messages) {
                    toast('Отлично', result.messages);
                }
                el.remove();
            },
            onFail: function (error) {
                toast('Ошибка', error.messages);
            }
        });
    }

    static created(event) {
        let formData = new FormData(event.target);
        let adsTable = document.querySelector('#usersTable');

        if (FormValidate.isValid(formData)) {
            this.post(this.addApi, formData, {
                onDone: function (result) {
                    event.target.reset();
                    adsTable.insertAdjacentHTML(
                        "afterbegin", result.save);

                    Client.deleteButton();
                    alert(result.messages, 'success')

                },
                onFail: function (error) {
                    FormValidate.getErrorForm(error.messages, event.target);
                },
                showProgress: function () {
                    butloading(event.submitter, true)
                },
                hideProgress: function () {
                    butloading(event.submitter, false, 'Добавить')
                }
            });
        }
    }
}
new Client();
