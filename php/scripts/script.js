var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var Web;
(function (Web) {
    var _a, _b, _c, _d, _e, _f, _g, _h;
    // classy
    class Temp {
    }
    Temp.modalOpened = null;
    Temp.overflowYBlocked = false;
    Temp.LecturerCalendar = {
        originalMonthText: (_b = ((_a = document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext")) === null || _a === void 0 ? void 0 : _a.innerText.trim()) + "") !== null && _b !== void 0 ? _b : null,
        monthText: (_d = ((_c = document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext")) === null || _c === void 0 ? void 0 : _c.innerText.trim()) + "") !== null && _d !== void 0 ? _d : null,
        controller: new AbortController(),
        loading: false,
        showOlder: false,
    };
    Temp.LecturerCalendarInAccount = {
        originalMonthText: (_f = ((_e = document.querySelector("#lecturer-calendar .bottom .month-controls .maintext")) === null || _e === void 0 ? void 0 : _e.innerText.trim()) + "") !== null && _f !== void 0 ? _f : null,
        monthText: (_h = ((_g = document.querySelector("#lecturer-calendar .bottom .month-controls .maintext")) === null || _g === void 0 ? void 0 : _g.innerText.trim()) + "") !== null && _h !== void 0 ? _h : null,
        controller: new AbortController(),
        loading: false,
    };
    Temp.accountSelectedWindow = "calendar";
    Web.Temp = Temp;
    class Modals {
        static main() {
            const blurElement = document.getElementById("modalblur");
            if (Temp.modalOpened) {
                blurElement.style.display = "grid";
                Temp.overflowYBlocked = true;
            }
            else {
                blurElement.style.display = "none";
                Temp.overflowYBlocked = false;
            }
        }
        static openModal(id) {
            if (Temp.modalOpened) {
                document.getElementById(Temp.modalOpened).style.display = "none";
                Temp.modalOpened = null;
            }
            else {
                const modal = document.getElementById(`modal-${id}`);
                if (!modal) {
                    console.error("Při volání funkce openModal nebyl nalezen hledaný modal.");
                    return;
                }
                modal.style.display = "grid";
                if (id === "lecturercalendar") {
                    modal.style.display = "flex";
                    if (document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext"))
                        document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext").innerText = Temp.LecturerCalendar.originalMonthText;
                    LecturerCalendar.resetCalendar();
                }
                Temp.modalOpened = `modal-${id}`;
            }
            this.main();
        }
    }
    Web.Modals = Modals;
    class LecturerCalendar {
        static getMonthNumber(monthName) {
            const months = [
                "leden", "únor", "březen", "duben", "květen", "červen",
                "červenec", "srpen", "září", "říjen", "listopad", "prosinec"
            ];
            return months.indexOf(monthName.toLowerCase());
        }
        static resetCalendar(complete = true) {
            if (!complete) {
                const a = Temp.LecturerCalendar.monthText;
                Temp.LecturerCalendar.monthText = "";
                Temp.LecturerCalendar.monthText = a;
            }
            else
                Temp.LecturerCalendar.monthText = Temp.LecturerCalendar.originalMonthText + "";
            if (document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent"))
                document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").innerHTML = "";
            if (document.querySelector("#modal-lecturercalendar .bottom .month-controls"))
                document.querySelector("#modal-lecturercalendar .bottom .month-controls").style.display = "flex";
            if (document.querySelector("#modal-lecturercalendar .bottom .flex"))
                document.querySelector("#modal-lecturercalendar .bottom .flex").style.display = "flex";
            if (document.querySelector("#modal-lecturercalendar .bottom .confirmation"))
                document.querySelector("#modal-lecturercalendar .bottom .confirmation").style.display = "none";
            this.render();
        }
        static resetSelectedDay() {
            Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #calendar table tbody tr td")).forEach((td) => { td.classList.remove("selected"); });
        }
        static setMonth(direction) {
            const element = document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext");
            const controls = document.querySelectorAll("#modal-lecturercalendar .bottom .month-controls div");
            const text = Temp.LecturerCalendar.monthText;
            let words = text.split(" ");
            if (words.length !== 2)
                return console.error("Invalid text format: " + text);
            let month = words[0];
            let year = parseInt(words[1]);
            let months = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
            let index = months.indexOf(month);
            if (year >= 2100)
                year = 2100;
            if (year <= 1900)
                year = 1900;
            if (index === -1 || isNaN(year))
                return console.error("Invalid month or year: " + month + " " + year);
            if (direction === "previous") {
                if (index === 0) {
                    year--;
                    month = "Prosinec";
                }
                else {
                    month = months[index - 1];
                }
            }
            else if (direction === "coming") {
                if (index === 11) {
                    year++;
                    month = "Leden";
                }
                else {
                    month = months[index + 1];
                }
            }
            else {
                return console.error("Invalid direction: " + direction);
            }
            this.resetSelectedDay();
            Temp.LecturerCalendar.monthText = month + " " + year;
            if (element)
                element.innerText = Temp.LecturerCalendar.monthText;
            if (document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent"))
                document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").remove();
            // zamknutí tlačítek na chvíli
            controls.forEach(control => control.style.pointerEvents = "none");
            setTimeout(() => {
                controls.forEach(control => control.style.pointerEvents = "unset");
            }, 25);
        }
        static render() {
            // loading
            if (Temp.LecturerCalendar.loading) {
                Temp.LecturerCalendar.loading = true;
                return;
            }
            // resetování kalendáře
            const calendar = document.querySelector("#modal-lecturercalendar .bottom #calendar");
            if (!calendar)
                return;
            Array.from(calendar.querySelectorAll("table tbody tr")).forEach(tr => {
                tr.innerHTML = "";
            });
            const loadingDiv = calendar.querySelector(".loadingdiv");
            if (loadingDiv)
                loadingDiv.style.display = "grid";
            calendar.querySelector("table").style.display = "none";
            Temp.LecturerCalendar.loading = true;
            // kontaktování api aby se doplnily data
            fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], { method: "get", signal: Temp.LecturerCalendar.controller.signal })
                .then(response => response.json())
                .then(data => {
                //region proměnné
                const daysInWeek = ["Po", "Út", "St", "Čt", "Pá", "So", "Ne"]; // musí se začínat od neděle kvůli Date objektu
                const monthText = Temp.LecturerCalendar.monthText + "";
                const words = monthText.split(" ");
                const monthName = words[0];
                const year = parseInt(words[1]);
                const selectedMonth = new Date(year, this.getMonthNumber(monthName), 1);
                const nextMonth = new Date(year, selectedMonth.getMonth() + 1, 1);
                const lastDayOfMonth = new Date(nextMonth - 1);
                const maxDaysInMonth = lastDayOfMonth.getDate();
                const currentMonthReservations = data.filter((reservation) => {
                    const reservationDate = new Date(reservation.date.split(" ")[0]);
                    const reservationMonth = reservationDate.getMonth();
                    const reservationYear = reservationDate.getFullYear();
                    const selectedMonthNumber = selectedMonth.getMonth();
                    const selectedYear = selectedMonth.getFullYear();
                    return reservationMonth === selectedMonthNumber && reservationYear === selectedYear;
                });
                const reservationsByDay = Array.from({ length: maxDaysInMonth }, () => []);
                currentMonthReservations.forEach((reservation) => {
                    const reservationDate = new Date(reservation.date);
                    const dayOfMonth = reservationDate.getDate();
                    reservationsByDay[dayOfMonth - 1].push(reservation);
                });
                //endregion
                // loop pro každý den v kalendáři
                let d = 1;
                weekloop: for (let weekI = 0; weekI < 6 /*(max 5 týdnů v měsíci)*/; weekI++) {
                    const row = calendar.querySelector(`table tbody tr:nth-child(${weekI + 1})`);
                    // nastavení dnů do jednotlivých týdnů + nastavení designu
                    let dayI = 0;
                    if (weekI == 0 && d == 1) {
                        dayI = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1).getDay() - 1;
                        if (dayI == -1)
                            dayI = 6;
                        for (let i = 0; i < dayI; i++) {
                            row.innerHTML += `<td></td>`;
                        }
                    }
                    // rendrování dnů do týdne
                    for (; dayI < 7; dayI++) {
                        if (d > maxDaysInMonth)
                            break weekloop;
                        const reservationsForDay = reservationsByDay[d - 1];
                        const dayIsPast = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d + 1).getTime() < new Date().getTime();
                        reservationsForDay.sort((a, b) => {
                            const dateA = new Date(a.date);
                            const dateB = new Date(b.date);
                            return dateA - dateB;
                        });
                        Web.LecturerCalendar.reservationsForDay = reservationsForDay;
                        let cellClass = "";
                        const date = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d);
                        if (reservationsForDay.some(reservation => reservation.user === "You")) {
                            cellClass = "reserved-you";
                        }
                        else if (reservationsForDay.length > 1 && reservationsForDay.some(reservation => reservation.user != null) && reservationsForDay.some(reservation => reservation.user == null)) {
                            cellClass = "reserved-not-full";
                        }
                        else if ((reservationsForDay.length === 1 && reservationsForDay[0].user != null) || (reservationsForDay.length > 1 && reservationsForDay.every(reservation => reservation.user != null))) {
                            cellClass = "reserved-not-you";
                        }
                        else if (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user == null)) {
                            cellClass = "not-reserved";
                        }
                        if (cellClass != "" && !dayIsPast) {
                            row.innerHTML += `<td class="${cellClass}" onclick="Web.LecturerCalendar.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                        }
                        else {
                            row.innerHTML += `<td>${d}</td>`;
                        }
                        if (d == new Date().getDate() && date.getMonth() == new Date().getMonth() && date.getFullYear() == new Date().getFullYear()) {
                            LecturerCalendar.selectDay(date, reservationsForDay);
                        }
                        d++;
                    }
                }
            }).then(() => {
                calendar.querySelector("table").style.display = "table";
                if (loadingDiv)
                    loadingDiv.style.display = "none";
                Temp.LecturerCalendar.loading = false;
            });
        }
        static selectDay(date, reservations) {
            const monthText = Temp.LecturerCalendar.monthText + "";
            const words = monthText.split(" ");
            const month = words[0];
            const year = parseInt(words[1]);
            if (document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent"))
                document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").remove();
            document.querySelector("#modal-lecturercalendar .bottom #dayprops").innerHTML += `<div class="days-parent"></div>`;
            const daysDiv = document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent");
            const numberElement = Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #calendar table tbody tr td")).find(td => { return new Date(year, this.getMonthNumber(month), parseInt(td.textContent)).getTime() === new Date(date).getTime(); });
            // odstranění třídy selected z předchozího vybraného dne a přidání nového
            this.resetSelectedDay();
            numberElement.classList.add("selected");
            // odstranění věcí z předchozího vybraného dne
            daysDiv.innerHTML = "";
            reservations.forEach((reservation) => {
                var _a;
                const date = new Date(reservation.date);
                const dateToday = new Date();
                const uuid = (_a = reservation.uuid) !== null && _a !== void 0 ? _a : null;
                const time = date.toLocaleTimeString("cs-CZ", { timeZone: "Europe/Prague" }).split(":").slice(0, 2).join(":");
                const getStatus = () => {
                    if (reservation.user === "You")
                        return "Rezervováno vámi";
                    if (reservation.user === "Not you")
                        return "Rezervováno";
                    return "Volné";
                };
                const getStyleClass = () => {
                    if (reservation.user === "You")
                        return "reserved-you";
                    if (reservation.user === "Not you")
                        return "reserved-not-you";
                    return "not-reserved";
                };
                const insertButtons = () => {
                    if (dateToday.getTime() < date.getTime())
                        return `
                        <div class="reservebutton" title="Rezerovovat" onclick="Web.LecturerCalendar.sendCalendarActionConfirmation('reserve', '${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${time} - ${parseInt(time.split(":")[0]) + 1}:00', '${uuid}')"></div>
                        <div class="reserve-delete-button" title="Smazat rezervaci" onclick="Web.LecturerCalendar.sendCalendarActionConfirmation('delete', '${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${time} - ${parseInt(time.split(":")[0]) + 1}:00', '${uuid}')"></div>
                    `;
                    return "";
                };
                daysDiv.innerHTML += `
                    <div class="lesson ${getStyleClass()}">
                        <p class="time">${time} - ${parseInt(time.split(":")[0]) + 1}:00</p>
                        <p class="status">${getStatus()}</p>
                        ${insertButtons()}
                    </div>`;
            });
            if (Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #dayprops .days-parent div")).length == 0) {
                daysDiv.innerHTML += `<p>Nebyly nalezeny žádné lekce na tento den.</p>`;
            }
        }
        static sendCalendarActionConfirmation(type, datetime, uuid) {
            return __awaiter(this, void 0, void 0, function* () {
                if (document.querySelector("#modal-lecturercalendar .bottom .month-controls") && document.querySelector("#modal-lecturercalendar .bottom .flex")) {
                    document.querySelector("#modal-lecturercalendar .bottom .month-controls").style.display = "none";
                    document.querySelector("#modal-lecturercalendar .bottom .flex").style.display = "none";
                    document.querySelector("#modal-lecturercalendar .bottom .confirmation").style.display = "block";
                }
                const confirmationDiv = document.querySelector("#modal-lecturercalendar .bottom .confirmation .center");
                let text;
                if (type === "delete") {
                    text = `Jste si jistý/á, že chcete <span style="text-decoration: underline">vymazat</span> rezervaci <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`;
                    confirmationDiv.innerHTML = `<p>${text}</p>` + "\n" + `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;
                }
                if (type === "reserve") {
                    text = `Opravdu chcete rezervovat termín <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`;
                    const userIsLogged = yield getIfUserIsLoggedIn();
                    confirmationDiv.innerHTML = `<p>${text + "\n "}</p>`;
                    if (!userIsLogged) {
                        // @ts-ignore
                        window.recaptchaCallback = function (response) {
                            // Zde zpracujte odpověď reCAPTCHA
                        };
                        // let script = document.createElement('script');
                        // script.type = 'text/javascript';
                        // script.src = 'https://www.google.com/recaptcha/api.js?render=6LcqGoMpAAAAAC3hKDADRS99IhxjEIxtrPngOPkq';
                        //
                        // document.head.appendChild(script);
                        confirmationDiv.innerHTML = `
                        <form id="reservationForm">
                            <div style="display: flex; flex-direction: column; gap: 5px">
                                <input type="text" name="first_name" placeholder="Jméno" required>
                                <input type="text" name="last_name" placeholder="Příjmení" required>
                                <input type="email" name="email" placeholder="Email" required pattern="[a-z0-9._%+\\-]+@[a-z0-9.\\-]+\\.[a-z]{2,4}$$">
                                <input type="text" name="mobile_number" placeholder="Telefonní číslo" required pattern="^(?:\\+\\d{3}\\s?\\d{3}\\s?\\d{3}\\s?\\d{3}|\\d{3}\\s?\\d{3}\\s?\\d{3})$">  
                                <div class="g-recaptcha" data-sitekey="6LcqGoMpAAAAAC3hKDADRS99IhxjEIxtrPngOPkq" data-callback="recaptchaCallback"></div>
                            </div>
                            
                            <div class="buttons" style="width: max-content">
                                <button style="width: 205px" type="submit" class="button-primary">Rezervovat</button>
                                <button style="width: 205px" class="button-secondary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'no', '${uuid}')">Zrušit</button>
                            </div>
                        </form>
                    `;
                    }
                    else
                        confirmationDiv.innerHTML += `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;
                    if (document.getElementById("reservationForm"))
                        document.getElementById("reservationForm").addEventListener("submit", function (event) {
                            event.preventDefault();
                            const formElement = event.target;
                            // const response = grecaptcha.getResponse();
                            // if (response.length == 0) return;
                            const formData = new FormData(formElement);
                            const requestBody = { uuid: uuid, first_name: formData.get("first_name"), last_name: formData.get("last_name"), email: formData.get("email"), mobile_number: formData.get("mobile_number") };
                            fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                                method: "put", body: JSON.stringify(requestBody)
                            }).then(() => { Web.LecturerCalendar.resetCalendar(false); });
                        });
                }
            });
        }
        static sendCalAction(action, decision, uuid) {
            if (decision === "no")
                return this.resetCalendar();
            switch (action) {
                case "delete":
                    {
                        fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                            method: "delete", body: JSON.stringify({ uuid: uuid })
                        }).then(() => { Web.LecturerCalendar.resetCalendar(false); });
                    }
                    break;
                case "reserve":
                    {
                        fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                            method: "put", body: JSON.stringify({ uuid: uuid })
                        }).then(() => { Web.LecturerCalendar.resetCalendar(false); });
                    }
                    break;
            }
        }
    }
    LecturerCalendar.reservationsForDay = null;
    Web.LecturerCalendar = LecturerCalendar;
    class LecturerCalendarInAccount {
        static getMonthNumber(monthName) {
            const months = [
                "leden", "únor", "březen", "duben", "květen", "červen",
                "červenec", "srpen", "září", "říjen", "listopad", "prosinec"
            ];
            return months.indexOf(monthName.toLowerCase());
        }
        static resetCalendar() {
            // Temp.LecturerCalendarInAccount.monthText = Temp.LecturerCalendarInAccount.originalMonthText + "";
            const monthText = Temp.LecturerCalendarInAccount.monthText;
            Temp.LecturerCalendarInAccount.monthText = "";
            Temp.LecturerCalendarInAccount.monthText = monthText;
            if (document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent"))
                document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").innerHTML = "";
            if (document.querySelector("#lecturer-calendar .bottom .month-controls"))
                document.querySelector("#lecturer-calendar .bottom .month-controls").style.display = "flex";
            if (document.querySelector("#lecturer-calendar .bottom .flex"))
                document.querySelector("#lecturer-calendar .bottom .flex").style.display = "flex";
            if (document.querySelector("#lecturer-calendar .bottom .confirmation"))
                document.querySelector("#lecturer-calendar .bottom .confirmation").style.display = "none";
            this.render();
        }
        static resetSelectedDay() {
            Array.from(document.querySelectorAll("#lecturer-calendar .bottom #calendar table tbody tr td")).forEach((td) => { td.classList.remove("selected"); });
        }
        static setMonth(direction) {
            const element = document.querySelector("#lecturer-calendar .bottom .month-controls .maintext");
            const controls = document.querySelectorAll("#lecturer-calendar .bottom .month-controls div");
            const text = Temp.LecturerCalendarInAccount.monthText;
            let words = text.split(" ");
            if (words.length !== 2)
                return console.error("Invalid text format: " + text);
            let month = words[0];
            let year = parseInt(words[1]);
            let months = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
            let index = months.indexOf(month);
            if (year >= 2100)
                year = 2100;
            if (year <= 1900)
                year = 1900;
            if (index === -1 || isNaN(year))
                return console.error("Invalid month or year: " + month + " " + year);
            if (direction === "previous") {
                if (index === 0) {
                    year--;
                    month = "Prosinec";
                }
                else {
                    month = months[index - 1];
                }
            }
            else if (direction === "coming") {
                if (index === 11) {
                    year++;
                    month = "Leden";
                }
                else {
                    month = months[index + 1];
                }
            }
            else {
                return console.error("Invalid direction: " + direction);
            }
            this.resetSelectedDay();
            Temp.LecturerCalendarInAccount.monthText = month + " " + year;
            if (element)
                element.innerText = Temp.LecturerCalendarInAccount.monthText;
            if (document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent"))
                document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").remove();
            // zamknutí tlačítek na chvíli
            controls.forEach(control => control.style.pointerEvents = "none");
            setTimeout(() => {
                controls.forEach(control => control.style.pointerEvents = "unset");
            }, 25);
        }
        static render() {
            // loading
            if (Temp.LecturerCalendarInAccount.loading) {
                Temp.LecturerCalendarInAccount.loading = true;
                return;
            }
            // resetování kalendáře
            const calendar = document.querySelector("#lecturer-calendar .bottom #calendar");
            if (!calendar)
                return;
            Array.from(calendar.querySelectorAll("table tbody tr")).forEach(tr => {
                tr.innerHTML = "";
            });
            const loadingDiv = calendar.querySelector(".loadingdiv");
            if (loadingDiv)
                loadingDiv.style.display = "grid";
            calendar.querySelector("table").style.display = "none";
            Temp.LecturerCalendarInAccount.loading = true;
            // kontaktování api aby se doplnily data
            fetch("/api/reservations/", { method: "get", signal: Temp.LecturerCalendarInAccount.controller.signal })
                .then(response => response.json())
                .then(data => {
                //region proměnné
                const daysInWeek = ["Po", "Út", "St", "Čt", "Pá", "So", "Ne"]; // musí se začínat od neděle kvůli Date objektu
                const monthText = Temp.LecturerCalendarInAccount.monthText + "";
                const words = monthText.split(" ");
                const monthName = words[0];
                const year = parseInt(words[1]);
                const selectedMonth = new Date(year, this.getMonthNumber(monthName), 1);
                const nextMonth = new Date(year, selectedMonth.getMonth() + 1, 1);
                const lastDayOfMonth = new Date(nextMonth - 1);
                const maxDaysInMonth = lastDayOfMonth.getDate();
                const currentMonthReservations = data.filter((reservation) => {
                    const reservationDate = new Date(reservation.date.split(" ")[0]);
                    const reservationMonth = reservationDate.getMonth();
                    const reservationYear = reservationDate.getFullYear();
                    const selectedMonthNumber = selectedMonth.getMonth();
                    const selectedYear = selectedMonth.getFullYear();
                    return reservationMonth === selectedMonthNumber && reservationYear === selectedYear;
                });
                const reservationsByDay = Array.from({ length: maxDaysInMonth }, () => []);
                currentMonthReservations.forEach((reservation) => {
                    const reservationDate = new Date(reservation.date);
                    const dayOfMonth = reservationDate.getDate();
                    reservationsByDay[dayOfMonth - 1].push(reservation);
                });
                //endregion
                // loop pro každý den v kalendáři
                let d = 1;
                weekloop: for (let weekI = 0; weekI < 6 /*(max 5 týdnů v měsíci)*/; weekI++) {
                    const row = calendar.querySelector(`table tbody tr:nth-child(${weekI + 1})`);
                    // nastavení dnů do jednotlivých týdnů + nastavení designu
                    let dayI = 0;
                    if (weekI == 0 && d == 1) {
                        dayI = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1).getDay() - 1;
                        if (dayI == -1)
                            dayI = 6;
                        for (let i = 0; i < dayI; i++) {
                            row.innerHTML += `<td></td>`;
                        }
                    }
                    // rendrování dnů do týdne
                    for (; dayI < 7; dayI++) {
                        if (d > maxDaysInMonth)
                            break weekloop;
                        const reservationsForDay = reservationsByDay[d - 1];
                        reservationsForDay.sort((a, b) => {
                            const dateA = new Date(a.date);
                            const dateB = new Date(b.date);
                            return dateA - dateB;
                        });
                        Web.LecturerCalendarInAccount.reservationsForDay = reservationsForDay;
                        let cellClass = "";
                        const date = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d);
                        if (reservationsForDay.length > 1 && reservationsForDay.some(reservation => reservation.user != null) && reservationsForDay.some(reservation => reservation.user == null)) {
                            cellClass = "reserved-not-full";
                        }
                        else if ((reservationsForDay.length === 1 && reservationsForDay[0].user != null) || (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user != null))) {
                            cellClass = "reserved-not-you";
                        }
                        else if (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user == null)) {
                            cellClass = "not-reserved";
                        }
                        if (cellClass != "") {
                            row.innerHTML += `<td class="${cellClass}" onclick="Web.LecturerCalendarInAccount.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                        }
                        else {
                            row.innerHTML += `<td onclick="Web.LecturerCalendarInAccount.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                        }
                        if (d == new Date().getDate() && date.getMonth() == new Date().getMonth() && date.getFullYear() == new Date().getFullYear()) {
                            LecturerCalendarInAccount.selectDay(date, reservationsForDay);
                        }
                        d++;
                    }
                }
            }).then(() => {
                calendar.querySelector("table").style.display = "table";
                if (loadingDiv)
                    loadingDiv.style.display = "none";
                Temp.LecturerCalendarInAccount.loading = false;
            });
        }
        static selectDay(date, reservations) {
            const monthText = Temp.LecturerCalendarInAccount.monthText + "";
            const words = monthText.split(" ");
            const month = words[0];
            const year = parseInt(words[1]);
            if (document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent"))
                document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").remove();
            document.querySelector("#lecturer-calendar .bottom #dayprops").innerHTML += `<div class="days-parent"></div>`;
            const daysDiv = document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent");
            const numberElement = Array.from(document.querySelectorAll("#lecturer-calendar .bottom #calendar table tbody tr td")).find(td => { return new Date(year, this.getMonthNumber(month), parseInt(td.textContent)).getTime() === new Date(date).getTime(); });
            // odstranění třídy selected z předchozího vybraného dne a přidání nového
            this.resetSelectedDay();
            numberElement.classList.add("selected");
            // odstranění věcí z předchozího vybraného dne
            daysDiv.innerHTML = "";
            reservations.forEach((reservation) => {
                var _a;
                const date = new Date(reservation.date);
                const dateToday = new Date();
                const uuid = (_a = reservation.uuid) !== null && _a !== void 0 ? _a : null;
                const time = date.toLocaleTimeString("cs-CZ", { timeZone: "Europe/Prague" }).split(":").slice(0, 2).join(":");
                const getStatus = () => {
                    var _a, _b, _c, _d, _e;
                    if (reservation.user != null)
                        return `Rezervováno: <br>&nbsp;<br>${(_a = reservation.user) !== null && _a !== void 0 ? _a : ""}<br>${(_b = reservation.user_first_name) !== null && _b !== void 0 ? _b : ""} ${(_c = reservation.user_last_name) !== null && _c !== void 0 ? _c : ""}<br>${(_d = reservation.user_email) !== null && _d !== void 0 ? _d : ""}<br>${(_e = reservation.user_mobilenumbers) !== null && _e !== void 0 ? _e : ""}`;
                    return "Volné";
                };
                const getStyleClass = () => {
                    if (reservation.user != null)
                        return "reserved-not-you";
                    return "not-reserved";
                };
                const insertButtons = () => {
                    return `
                        <div class="reserve-delete-button" title="Smazat rezervaci" onclick="Web.LecturerCalendarInAccount.sendCalendarActionConfirmation('delete', '${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${time} - ${parseInt(time.split(":")[0]) + 1}:00', '${uuid}')"></div>
                    `;
                };
                daysDiv.innerHTML += `
                    <div class="lesson ${getStyleClass()}">
                        <p class="time">${time} - ${parseInt(time.split(":")[0]) + 1}:00</p>
                        <p class="status">${getStatus()}</p>
                        ${insertButtons()}
                    </div>
                `;
            });
            if (Array.from(document.querySelectorAll("#lecturer-calendar .bottom #dayprops .days-parent div")).length == 0) {
                daysDiv.innerHTML += `<p>Nebyly nalezeny žádné lekce na tento den.</p>`;
            }
            daysDiv.innerHTML += `<p class="add" onclick="Web.LecturerCalendarInAccount.sendCalendarActionConfirmation('reserve', '${date}', null)">Přidat termín</p>`;
        }
        static sendCalendarActionConfirmation(type, datetime, uuid) {
            if (document.querySelector("#lecturer-calendar .bottom .month-controls") && document.querySelector("#lecturer-calendar .bottom .flex")) {
                document.querySelector("#lecturer-calendar .bottom .month-controls").style.display = "none";
                document.querySelector("#lecturer-calendar .bottom .flex").style.display = "none";
                document.querySelector("#lecturer-calendar .bottom .confirmation").style.display = "block";
            }
            const confirmationDiv = document.querySelector("#lecturer-calendar .bottom .confirmation .center");
            let text;
            let inputhtml = "";
            if (type === "delete") {
                text = `Jste si jistý/á, že chcete <span style="text-decoration: underline">vymazat</span> rezervaci <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`;
                confirmationDiv.innerHTML = `<p>${text}</p>` + "\n" + `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendarInAccount.sendCalAction('delete', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendarInAccount.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;
            }
            else if (type === "reserve") {
                text = `<span style="color: var(--color-main); font-weight: bolder">Nový termín</span>`;
                confirmationDiv.innerHTML = `
                    <p>${text}</p>
                    
                    <form id="reservationForm" onsubmit="event.preventDefault(); Web.LecturerCalendarInAccount.addNewReservation('${datetime}')">
                        <select name="timeselect" style="width: 100%; margin-top: 20px; font-family: opensans-medium, Calibri, sans-serif">
                            <option value="0" >0:00 - 1:00</option>
                            <option value="1" >1:00 - 2:00</option>
                            <option value="2" >2:00 - 3:00</option>
                            <option value="3" >3:00 - 4:00</option>
                            <option value="4" >4:00 - 5:00</option>
                            <option value="5" >5:00 - 6:00</option>
                            <option value="6" >6:00 - 7:00</option>
                            <option value="7" >7:00 - 8:00</option>
                            <option value="8" >8:00 - 9:00</option>
                            <option value="9" >9:00 - 10:00</option>
                            <option value="10">10:00 - 11:00</option>
                            <option value="11">11:00 - 12:00</option>
                            <option value="12">12:00 - 13:00</option>
                            <option value="13">13:00 - 14:00</option>
                            <option value="14">14:00 - 15:00</option>
                            <option value="15">15:00 - 16:00</option>
                            <option value="16">16:00 - 17:00</option>
                            <option value="17">17:00 - 18:00</option>
                            <option value="18">18:00 - 19:00</option>
                            <option value="19">19:00 - 20:00</option>
                            <option value="20">20:00 - 21:00</option>
                            <option value="21">21:00 - 22:00</option>
                            <option value="22">22:00 - 23:00</option>
                            <option value="23">23:00 - 23:59</option>
                        </select>
                        
                        <div class="buttons">
                            <input type="submit" class="button-primary" value="Přidat" />
                            <button class="button-secondary" onclick="event.preventDefault(); Web.LecturerCalendarInAccount.sendCalAction('reserve', 'no', null)">Zrušit</button>
                        </div>
                    </form>
                `;
            }
        }
        static sendCalAction(action, decision, uuid) {
            if (decision === "no")
                return this.resetCalendar();
            switch (action) {
                case "delete":
                    {
                        fetch("/api/reservations/", {
                            method: "COMPLETEDELETE", body: JSON.stringify({ uuid: uuid })
                        }).then(() => { Web.LecturerCalendarInAccount.resetCalendar(); });
                    }
                    break;
            }
        }
        static addNewReservation(date) {
            const parsedDate = /*new Date(*/ new Date(date) /*.toLocaleString('cs-CZ', { timeZone: 'Europe/Prague' }))*/;
            const dateString = parsedDate.getFullYear() + "-" + (parsedDate.getMonth() + 1) + "-" + parsedDate.getDate();
            const form = document.getElementById('reservationForm');
            const formData = new FormData(form);
            const datetime = dateString + " " + formData.get("timeselect") + ":00";
            fetch("/api/reservations/", {
                method: "post", body: JSON.stringify({ date: datetime })
            }).then(() => { Web.LecturerCalendarInAccount.resetCalendar(); });
        }
        static generateICSContent(reservations) {
            let content = `BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Moje rezervace//EN\n`;
            for (let reservation of reservations) {
                const { date, user, uuid, user_email, user_last_name, user_first_name, user_mobilenumbers } = reservation;
                if (!user || !uuid || !date)
                    continue;
                if (new Date(date).getTime() < new Date().getTime())
                    continue;
                const startDate = new Date(date).toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '');
                const endDate = new Date(date);
                endDate.setHours(endDate.getHours() + 1);
                const endDateString = endDate.toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '');
                content += `BEGIN:VEVENT\n`;
                content += `UID:${uuid}\n`;
                content += `DTSTART:${startDate}\n`;
                content += `DTEND:${endDateString}\n`;
                content += `SUMMARY:${user_first_name + " " + user_last_name} - Teacher Digital Agency - schůzka\n`;
                content += `DESCRIPTION:Rezervováno uživatelem: ${user}; ${user_first_name} ${user_last_name}; ${user_email}; ${user_mobilenumbers}\n`;
                if (user_email)
                    content += `ORGANIZER;CN=${user_first_name} ${user_last_name}:mailto:${user_email}\n`;
                if (user_mobilenumbers && user_mobilenumbers.length > 0)
                    content += `TEL;VALUE=uri:tel:${user_mobilenumbers[0]}\n`;
                content += `END:VEVENT\n`;
            }
            content += `END:VCALENDAR\n`;
            return content;
        }
        static downloadCalendar() {
            fetch("/api/reservations/", { method: "get" })
                .then(response => response.json())
                .then((reservations) => {
                const icsContent = this.generateICSContent(reservations);
                const element = document.createElement('a');
                element.setAttribute('href', 'data:text/calendar;charset=utf-8,' + encodeURIComponent(icsContent));
                element.setAttribute('download', 'calendar.ics');
                element.style.display = 'none';
                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);
            });
        }
    }
    LecturerCalendarInAccount.reservationsForDay = null;
    Web.LecturerCalendarInAccount = LecturerCalendarInAccount;
    // funkce
    function main() {
        Web.onScroll();
        Web.setPageHeightPadding();
    }
    Web.main = main;
    function selectActivityBlock(id) {
        const el = document.getElementById(id);
        const y = el.getBoundingClientRect().top + window.scrollY - el.clientHeight - 80;
        window.scroll({
            top: y,
            behavior: 'smooth'
        });
    }
    Web.selectActivityBlock = selectActivityBlock;
    function activitiesRunLoadingAnimation() {
        const element = document.querySelector("#ACTIVITIES section");
        const loginInput = document.querySelector("#ACTIVITIES section form input[type='text']");
        const submitInput = document.querySelector("#ACTIVITIES section form input[type='submit']");
        const activitiesDiv = document.querySelector("#ACTIVITIES section .activities");
        if (!element) {
            console.error("Element not found");
            return;
        }
        const loadingElement = document.createElement("div");
        loadingElement.style.width = "200px";
        loadingElement.style.height = "200px";
        loadingElement.style.margin = "0 auto";
        loadingElement.style.marginTop = "50px";
        loadingElement.style.backgroundImage = "url('/images/icons/loading.svg')";
        loadingElement.style.backgroundPosition = "center";
        loadingElement.style.backgroundSize = "cover";
        loadingElement.style.backgroundRepeat = "no-repeat";
        element.append(loadingElement);
        if (activitiesDiv)
            activitiesDiv.remove();
        if (loginInput) {
            setTimeout(() => {
                loginInput.disabled = true;
                submitInput.disabled = true;
            }, 10);
        }
        else
            console.warn("neeixstuje login input");
    }
    Web.activitiesRunLoadingAnimation = activitiesRunLoadingAnimation;
    function scrollIntoView(selector, offset = 80) {
        window.scroll(0, document.querySelector(selector).offsetTop - offset);
    }
    Web.scrollIntoView = scrollIntoView;
    function onScroll() {
        const header = document.getElementById("HEADER");
        if (!header)
            return;
        const headerDesign = {
            default: { "background-color": "transparent", "position": "absolute", "backdrop-filter": "brightness(100%)", "display": "unset" },
            scrolled: { "background-color": "rgba(255,255,255,0.15)", "position": "fixed", "backdrop-filter": "brightness(10%)", "display": "unset" }
        };
        if (window.scrollY > 1) {
            for (const prop in headerDesign.scrolled) {
                header.style[prop] = headerDesign.scrolled[prop];
            }
        }
        else {
            for (const prop in headerDesign.default) {
                header.style[prop] = headerDesign.default[prop];
            }
        }
    }
    Web.onScroll = onScroll;
    function setPageHeightPadding() {
        const el = document.querySelector(".main");
        const y = (el === null || el === void 0 ? void 0 : el.clientHeight) - 100;
        if (y && y > window.screen.height)
            el === null || el === void 0 ? void 0 : el.classList.add("pb");
    }
    Web.setPageHeightPadding = setPageHeightPadding;
    function toggleMenu() {
        const menuIcon = document.getElementById("HEADER").getElementsByClassName("mobile-menu-icon")[0];
        const menuDiv = menuIcon.getElementsByClassName("mobile-menu")[0];
        if (menuDiv)
            menuDiv.style.display = menuDiv.style.display === "none" ? "block" : "none";
    }
    Web.toggleMenu = toggleMenu;
    function setElHeightToScrollHeight(element) {
        element.style.height = "40px";
        element.style.height = element.scrollHeight + 'px';
    }
    Web.setElHeightToScrollHeight = setElHeightToScrollHeight;
    function accountSelectWindow(type) {
        const el = document.querySelector("#ACCOUNT .window-selection");
        if (type == "calendar") {
            document.querySelector("#ACCOUNT #lecturer-calendar").style.display = "block";
            document.querySelector("#ACCOUNT .userinfointerface").style.display = "none";
            document.querySelector("#ACCOUNT .exportcal").style.display = "block";
        }
        else if (type == "settings") {
            document.querySelector("#ACCOUNT #lecturer-calendar").style.display = "none";
            document.querySelector("#ACCOUNT .exportcal").style.display = "none";
            document.querySelector("#ACCOUNT .userinfointerface").style.display = "flex";
        }
    }
    Web.accountSelectWindow = accountSelectWindow;
    function getIfUserIsLoggedIn() {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch("/api/user_is_logged");
            return yield response.json();
        });
    }
    Web.getIfUserIsLoggedIn = getIfUserIsLoggedIn;
})(Web || (Web = {}));
// region eventy
document.addEventListener("scroll", (event) => {
    Web.onScroll();
});
window.onload = () => {
    if (document.querySelector("#ACCOUNT"))
        Web.LecturerCalendarInAccount.resetCalendar();
    Web.main();
};
document.addEventListener("wheel", (event) => {
    if (Web.Temp.overflowYBlocked) {
        event.preventDefault(); // Zabrání běžnému scrollování kolečkem myši
    }
}, { passive: false });
const targetNode = document.getElementById("modal-lecturercalendar-monthcontrols-maintext");
const observer = new MutationObserver((mutationsList, observer) => {
    Web.LecturerCalendar.render();
});
const config = { childList: true, subtree: true };
if (targetNode)
    observer.observe(targetNode, config);
const targetNode1 = document.getElementById("lecturercalendar-monthcontrols-maintext");
const observer1 = new MutationObserver((mutationsList, observer) => {
    Web.LecturerCalendarInAccount.render();
});
const config1 = { childList: true, subtree: true };
if (targetNode1)
    observer1.observe(targetNode1, config1);
// endregion
