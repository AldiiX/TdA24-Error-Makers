declare var grecaptcha: any;

namespace Web {


    // classy
    export abstract class Temp {
        public static modalOpened: null | string = null;
        public static overflowYBlocked: boolean = false;

        public static LecturerCalendar = {
            originalMonthText: (document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext") as HTMLElement)?.innerText.trim() + "" ?? null,
            monthText: (document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext") as HTMLElement)?.innerText.trim() + "" ?? null,
            controller: new AbortController(),
            loading: false,
            showOlder: false,
        };

        public static LecturerCalendarInAccount = {
            originalMonthText: (document.querySelector("#lecturer-calendar .bottom .month-controls .maintext") as HTMLElement)?.innerText.trim() + "" ?? null,
            monthText: (document.querySelector("#lecturer-calendar .bottom .month-controls .maintext") as HTMLElement)?.innerText.trim() + "" ?? null,
            controller: new AbortController(),
            loading: false,
        };

        public static accountSelectedWindow: "calendar" | "settings" = "calendar";
    }

    export abstract class Modals {
        public static main(): void {
            const blurElement = document.getElementById("modalblur");

            if (Temp.modalOpened) {
                blurElement.style.display = "grid";
                Temp.overflowYBlocked = true;
            } else {
                blurElement.style.display = "none";
                Temp.overflowYBlocked = false;
            }
        }

        public static openModal(id: string | null): void {
            if (Temp.modalOpened) {
                document.getElementById(Temp.modalOpened).style.display = "none";
                Temp.modalOpened = null;
            } else {
                const modal = document.getElementById(`modal-${id}`);
                if (!modal) {
                    console.error("Při volání funkce openModal nebyl nalezen hledaný modal.");
                    return;
                }

                modal.style.display = "grid";
                if (id === "lecturercalendar") {
                    modal.style.display = "flex";
                    if (document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext")) (document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext") as HTMLElement).innerText = Temp.LecturerCalendar.originalMonthText;
                    LecturerCalendar.resetCalendar();
                }
                Temp.modalOpened = `modal-${id}`;
            }

            this.main();
        }
    }

    export abstract class LecturerCalendar {

        public static reservationsForDay: any[] = null;

        private static getMonthNumber(monthName: string) {
            const months = [
                "leden", "únor", "březen", "duben", "květen", "červen",
                "červenec", "srpen", "září", "říjen", "listopad", "prosinec"
            ];
            return months.indexOf(monthName.toLowerCase());
        }

        public static resetCalendar(complete: boolean = true): void {
            if(!complete) {
                const a = Temp.LecturerCalendar.monthText;
                Temp.LecturerCalendar.monthText = "";
                Temp.LecturerCalendar.monthText = a;
            } else Temp.LecturerCalendar.monthText = Temp.LecturerCalendar.originalMonthText + "";

            if(document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent")) document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").innerHTML = "";
            if(document.querySelector("#modal-lecturercalendar .bottom .month-controls")) (document.querySelector("#modal-lecturercalendar .bottom .month-controls") as HTMLElement).style.display = "flex";
            if(document.querySelector("#modal-lecturercalendar .bottom .flex")) (document.querySelector("#modal-lecturercalendar .bottom .flex") as HTMLElement).style.display = "flex";
            if(document.querySelector("#modal-lecturercalendar .bottom .confirmation")) (document.querySelector("#modal-lecturercalendar .bottom .confirmation") as HTMLElement).style.display = "none";
            this.render();
        }

        private static resetSelectedDay(): void {
            Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #calendar table tbody tr td")).forEach((td: HTMLElement) => { td.classList.remove("selected") });
        }

        public static setMonth(direction: "previous" | "coming"): void {
            const element: HTMLElement = document.querySelector("#modal-lecturercalendar .bottom .month-controls .maintext");
            const controls: NodeListOf<HTMLElement> = document.querySelectorAll("#modal-lecturercalendar .bottom .month-controls div");

            const text = Temp.LecturerCalendar.monthText;
            let words = text.split(" ");

            if (words.length !== 2) return console.error("Invalid text format: " + text);

            let month = words[0];
            let year = parseInt(words[1]);
            let months = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
            let index = months.indexOf(month);

            if (year >= 2100) year = 2100;
            if (year <= 1900) year = 1900;
            if (index === -1 || isNaN(year)) return console.error("Invalid month or year: " + month + " " + year);

            if (direction === "previous") {
                if (index === 0) {
                    year--;
                    month = "Prosinec";
                } else {
                    month = months[index - 1];
                }
            } else if (direction === "coming") {
                if (index === 11) {
                    year++;
                    month = "Leden";
                } else {
                    month = months[index + 1];
                }
            } else {
                return console.error("Invalid direction: " + direction);
            }

            this.resetSelectedDay();
            Temp.LecturerCalendar.monthText = month + " " + year;
            if (element) element.innerText = Temp.LecturerCalendar.monthText;
            if(document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent")) document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").remove();

            // zamknutí tlačítek na chvíli
            controls.forEach(control => control.style.pointerEvents = "none");
            setTimeout(() => {
                controls.forEach(control => control.style.pointerEvents = "unset");
            }, 25);
        }

        public static render(): void {

            // loading
            if (Temp.LecturerCalendar.loading) {
                Temp.LecturerCalendar.loading = true;
                return;
            }



            // resetování kalendáře
            const calendar: HTMLElement = document.querySelector("#modal-lecturercalendar .bottom #calendar");
            if(!calendar) return;

            Array.from(calendar.querySelectorAll("table tbody tr")).forEach(tr => {
                tr.innerHTML = "";
            });

            const loadingDiv: HTMLElement = calendar.querySelector(".loadingdiv");
            if (loadingDiv) loadingDiv.style.display = "grid";

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
                const nextMonth: any | Date = new Date(year, selectedMonth.getMonth() + 1, 1);
                const lastDayOfMonth = new Date(nextMonth - 1);
                const maxDaysInMonth = lastDayOfMonth.getDate();

                const currentMonthReservations = data.filter((reservation: { date: string; }) => {
                    const reservationDate = new Date(reservation.date.split(" ")[0]);
                    const reservationMonth = reservationDate.getMonth();
                    const reservationYear = reservationDate.getFullYear();
                    const selectedMonthNumber = selectedMonth.getMonth();
                    const selectedYear = selectedMonth.getFullYear();

                    return reservationMonth === selectedMonthNumber && reservationYear === selectedYear;
                });

                const reservationsByDay = Array.from({ length: maxDaysInMonth }, () => []);

                currentMonthReservations.forEach((reservation: { date: string | number | Date; }) => {
                    const reservationDate = new Date(reservation.date);
                    const dayOfMonth = reservationDate.getDate();
                    reservationsByDay[dayOfMonth - 1].push(reservation);
                });
                //endregion



                // loop pro každý den v kalendáři
                let d = 1;
                weekloop: for (let weekI = 0; weekI < 6 /*(max 5 týdnů v měsíci)*/; weekI++) {
                    const row: HTMLElement = calendar.querySelector(`table tbody tr:nth-child(${weekI + 1})`);



                    // nastavení dnů do jednotlivých týdnů + nastavení designu
                    let dayI = 0;
                    if(weekI == 0 && d == 1) {
                        dayI = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1).getDay() - 1;
                        if(dayI == -1) dayI = 6;

                        for (let i = 0; i < dayI; i++) {
                           row.innerHTML += `<td></td>`;
                        }
                    }

                    // rendrování dnů do týdne
                    for (; dayI < 7; dayI++) {
                        if (d > maxDaysInMonth) break weekloop;

                        const reservationsForDay = reservationsByDay[d - 1];
                        const dayIsPast = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d+1) .getTime() < new Date().getTime();

                        reservationsForDay.sort((a: { [key: string]: any }, b: { [key: string]: any }) => {
                            const dateA: any = new Date(a.date);
                            const dateB: any = new Date(b.date);
                            return dateA - dateB;
                        });

                        Web.LecturerCalendar.reservationsForDay = reservationsForDay;
                        let cellClass = "";
                        const date = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d);

                        if (reservationsForDay.some(reservation => reservation.user === "You")) {
                            cellClass = "reserved-you";
                        } else if (reservationsForDay.length > 1 && reservationsForDay.some(reservation => reservation.user != null) && reservationsForDay.some(reservation => reservation.user == null)) {
                            cellClass = "reserved-not-full";
                        } else if ((reservationsForDay.length === 1 && reservationsForDay[0].user != null) || (reservationsForDay.length > 1 && reservationsForDay.every(reservation => reservation.user != null))) {
                            cellClass = "reserved-not-you";
                        } else if (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user == null)) {
                            cellClass = "not-reserved";
                        }

                        if (cellClass != "" && !dayIsPast) {
                            row.innerHTML += `<td class="${cellClass}" onclick="Web.LecturerCalendar.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                        } else {
                            row.innerHTML += `<td>${d}</td>`;
                        }

                        if(d == new Date().getDate() && date.getMonth() == new Date().getMonth() && date.getFullYear() == new Date().getFullYear()) {
                            LecturerCalendar.selectDay(date, reservationsForDay);
                        }

                        d++;
                    }
                }
            }).then(() => {
                calendar.querySelector("table").style.display = "table";
                if (loadingDiv) loadingDiv.style.display = "none";
                Temp.LecturerCalendar.loading = false;
            });
        }

        public static selectDay(date: Date, reservations: any[]): void {
            const monthText: string = Temp.LecturerCalendar.monthText + "";
            const words: string[] = monthText.split(" ");
            const month: string  = words[0];
            const year: number = parseInt(words[1]);

            if(document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent")) document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent").remove();
            document.querySelector("#modal-lecturercalendar .bottom #dayprops").innerHTML += `<div class="days-parent"></div>`;
            const daysDiv: HTMLElement = document.querySelector("#modal-lecturercalendar .bottom #dayprops .days-parent");
            const numberElement: HTMLElement = Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #calendar table tbody tr td")).find(td => { return new Date(year, this.getMonthNumber(month), parseInt(td.textContent)).getTime() === new Date(date).getTime() }) as HTMLElement;



            // odstranění třídy selected z předchozího vybraného dne a přidání nového
            this.resetSelectedDay();
            numberElement.classList.add("selected");



            // odstranění věcí z předchozího vybraného dne
            daysDiv.innerHTML = "";

            reservations.forEach((reservation: { [key: string]: any }) => {
                const date = new Date(reservation.date);
                const dateToday = new Date();
                const uuid = reservation.uuid ?? null;
                const time = date.toLocaleTimeString("cs-CZ", { timeZone: "Europe/Prague"}).split(":").slice(0, 2).join(":");

                const getStatus = (): string => {
                    if (reservation.user === "You") return "Rezervováno vámi";
                    if (reservation.user === "Not you") return "Rezervováno";
                    return "Volné";
                };
                const getStyleClass = (): string => {
                    if (reservation.user === "You") return "reserved-you";
                    if (reservation.user === "Not you") return "reserved-not-you";
                    return "not-reserved";
                };
                const insertButtons = (): string => {
                    if (dateToday.getTime() < date.getTime()) return `
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

            if(Array.from(document.querySelectorAll("#modal-lecturercalendar .bottom #dayprops .days-parent div")).length == 0) {
                daysDiv.innerHTML += `<p>Nebyly nalezeny žádné lekce na tento den.</p>`;
            }
        }

        public static async sendCalendarActionConfirmation(type: "delete" | "reserve", datetime: string, uuid: string): Promise<void> {
            if(document.querySelector("#modal-lecturercalendar .bottom .month-controls") && document.querySelector("#modal-lecturercalendar .bottom .flex")) {
                (document.querySelector("#modal-lecturercalendar .bottom .month-controls") as HTMLElement).style.display = "none";
                (document.querySelector("#modal-lecturercalendar .bottom .flex") as HTMLElement).style.display = "none";
                (document.querySelector("#modal-lecturercalendar .bottom .confirmation") as HTMLElement).style.display = "block";
            }



            const confirmationDiv: HTMLElement = document.querySelector("#modal-lecturercalendar .bottom .confirmation .center");
            let text: string;
            if(type === "delete") {
                text = `Jste si jistý/á, že chcete <span style="text-decoration: underline">vymazat</span> rezervaci <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`
                confirmationDiv.innerHTML = `<p>${text}</p>` + "\n" + `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;
            }

            if(type === "reserve") {
                text = `Opravdu chcete rezervovat termín <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`
                const userIsLogged = await getIfUserIsLoggedIn();



                confirmationDiv.innerHTML = `<p>${text + "\n "}</p>`;
                if(!userIsLogged) {

                    // @ts-ignore
                    window.recaptchaCallback = function(response) {
                        // Zde zpracujte odpověď reCAPTCHA
                    }

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
                } else confirmationDiv.innerHTML += `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendar.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;

                if(document.getElementById("reservationForm")) document.getElementById("reservationForm").addEventListener("submit", function(event) {
                    event.preventDefault();

                    const formElement: HTMLFormElement = event.target as HTMLFormElement;
                    // const response = grecaptcha.getResponse();

                    // if (response.length == 0) return;


                    const formData: FormData = new FormData(formElement);
                    const requestBody = { uuid: uuid, first_name: formData.get("first_name"), last_name: formData.get("last_name"), email: formData.get("email"), mobile_number: formData.get("mobile_number")};

                    fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                        method: "put", body: JSON.stringify(requestBody)
                    }).then(() => {Web.LecturerCalendar.resetCalendar(false)});
                });
            }
        }

        public static sendCalAction(action: "delete" | "reserve" | string, decision: "yes" | "no", uuid: string): void {
            if(decision === "no") return this.resetCalendar();

            switch (action) {
                case "delete": {
                    fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                        method: "delete", body: JSON.stringify({ uuid: uuid })
                    }).then(() => {Web.LecturerCalendar.resetCalendar(false)});
                } break;

                case "reserve": {
                    fetch("/api/reservations/" + window.location.href.split('/')[window.location.href.split('/').length - 1], {
                        method: "put", body: JSON.stringify({ uuid: uuid })
                    }).then(() => {Web.LecturerCalendar.resetCalendar(false)});
                } break;
            }
        }
    }

    export abstract class LecturerCalendarInAccount {

        public static reservationsForDay: any[] = null;

        private static getMonthNumber(monthName: string) {
            const months = [
                "leden", "únor", "březen", "duben", "květen", "červen",
                "červenec", "srpen", "září", "říjen", "listopad", "prosinec"
            ];
            return months.indexOf(monthName.toLowerCase());
        }

        public static resetCalendar(): void {
            // Temp.LecturerCalendarInAccount.monthText = Temp.LecturerCalendarInAccount.originalMonthText + "";
            const monthText: string = Temp.LecturerCalendarInAccount.monthText;
            Temp.LecturerCalendarInAccount.monthText = "";
            Temp.LecturerCalendarInAccount.monthText = monthText;
            if(document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent")) document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").innerHTML = "";
            if(document.querySelector("#lecturer-calendar .bottom .month-controls")) (document.querySelector("#lecturer-calendar .bottom .month-controls") as HTMLElement).style.display = "flex";
            if(document.querySelector("#lecturer-calendar .bottom .flex")) (document.querySelector("#lecturer-calendar .bottom .flex") as HTMLElement).style.display = "flex";
            if(document.querySelector("#lecturer-calendar .bottom .confirmation")) (document.querySelector("#lecturer-calendar .bottom .confirmation") as HTMLElement).style.display = "none";
            this.render();
        }

        private static resetSelectedDay(): void {
            Array.from(document.querySelectorAll("#lecturer-calendar .bottom #calendar table tbody tr td")).forEach((td: HTMLElement) => { td.classList.remove("selected") });
        }

        public static setMonth(direction: "previous" | "coming"): void {
            const element: HTMLElement = document.querySelector("#lecturer-calendar .bottom .month-controls .maintext");
            const controls: NodeListOf<HTMLElement> = document.querySelectorAll("#lecturer-calendar .bottom .month-controls div");

            const text = Temp.LecturerCalendarInAccount.monthText;
            let words = text.split(" ");

            if (words.length !== 2) return console.error("Invalid text format: " + text);

            let month = words[0];
            let year = parseInt(words[1]);
            let months = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];
            let index = months.indexOf(month);

            if (year >= 2100) year = 2100;
            if (year <= 1900) year = 1900;
            if (index === -1 || isNaN(year)) return console.error("Invalid month or year: " + month + " " + year);

            if (direction === "previous") {
                if (index === 0) {
                    year--;
                    month = "Prosinec";
                } else {
                    month = months[index - 1];
                }
            } else if (direction === "coming") {
                if (index === 11) {
                    year++;
                    month = "Leden";
                } else {
                    month = months[index + 1];
                }
            } else {
                return console.error("Invalid direction: " + direction);
            }

            this.resetSelectedDay();
            Temp.LecturerCalendarInAccount.monthText = month + " " + year;
            if (element) element.innerText = Temp.LecturerCalendarInAccount.monthText;
            if(document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent")) document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").remove();

            // zamknutí tlačítek na chvíli
            controls.forEach(control => control.style.pointerEvents = "none");
            setTimeout(() => {
                controls.forEach(control => control.style.pointerEvents = "unset");
            }, 25);
        }

        public static render(): void {

            // loading
            if (Temp.LecturerCalendarInAccount.loading) {
                Temp.LecturerCalendarInAccount.loading = true;
                return;
            }



            // resetování kalendáře
            const calendar: HTMLElement = document.querySelector("#lecturer-calendar .bottom #calendar");
            if(!calendar) return;

            Array.from(calendar.querySelectorAll("table tbody tr")).forEach(tr => {
                tr.innerHTML = "";
            });

            const loadingDiv: HTMLElement = calendar.querySelector(".loadingdiv");
            if (loadingDiv) loadingDiv.style.display = "grid";

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
                    const nextMonth: any | Date = new Date(year, selectedMonth.getMonth() + 1, 1);
                    const lastDayOfMonth = new Date(nextMonth - 1);
                    const maxDaysInMonth = lastDayOfMonth.getDate();

                    const currentMonthReservations = data.filter((reservation: { date: string; }) => {
                        const reservationDate = new Date(reservation.date.split(" ")[0]);
                        const reservationMonth = reservationDate.getMonth();
                        const reservationYear = reservationDate.getFullYear();
                        const selectedMonthNumber = selectedMonth.getMonth();
                        const selectedYear = selectedMonth.getFullYear();

                        return reservationMonth === selectedMonthNumber && reservationYear === selectedYear;
                    });

                    const reservationsByDay = Array.from({ length: maxDaysInMonth }, () => []);

                    currentMonthReservations.forEach((reservation: { date: string | number | Date; }) => {
                        const reservationDate = new Date(reservation.date);
                        const dayOfMonth = reservationDate.getDate();
                        reservationsByDay[dayOfMonth - 1].push(reservation);
                    });
                    //endregion



                    // loop pro každý den v kalendáři
                    let d = 1;
                    weekloop: for (let weekI = 0; weekI < 6 /*(max 5 týdnů v měsíci)*/; weekI++) {
                        const row: HTMLElement = calendar.querySelector(`table tbody tr:nth-child(${weekI + 1})`);



                        // nastavení dnů do jednotlivých týdnů + nastavení designu
                        let dayI = 0;
                        if(weekI == 0 && d == 1) {
                            dayI = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1).getDay() - 1;
                            if(dayI == -1) dayI = 6;

                            for (let i = 0; i < dayI; i++) {
                                row.innerHTML += `<td></td>`;
                            }
                        }

                        // rendrování dnů do týdne
                        for (; dayI < 7; dayI++) {
                            if (d > maxDaysInMonth) break weekloop;

                            const reservationsForDay = reservationsByDay[d - 1];
                            reservationsForDay.sort((a: { [key: string]: any }, b: { [key: string]: any }) => {
                                const dateA: any = new Date(a.date);
                                const dateB: any = new Date(b.date);
                                return dateA - dateB;
                            });

                            Web.LecturerCalendarInAccount.reservationsForDay = reservationsForDay;
                            let cellClass = "";
                            const date = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), d);

                            if (reservationsForDay.length > 1 && reservationsForDay.some(reservation => reservation.user != null) && reservationsForDay.some(reservation => reservation.user == null)) {
                                cellClass = "reserved-not-full";
                            } else if ((reservationsForDay.length === 1 && reservationsForDay[0].user != null) || (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user != null))) {
                                cellClass = "reserved-not-you";
                            } else if (reservationsForDay.length > 0 && reservationsForDay.every(reservation => reservation.user == null)) {
                                cellClass = "not-reserved";
                            }

                            if (cellClass != "") {
                                row.innerHTML += `<td class="${cellClass}" onclick="Web.LecturerCalendarInAccount.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                            } else {
                                row.innerHTML += `<td onclick="Web.LecturerCalendarInAccount.selectDay(new Date('${date}'), ${JSON.stringify(reservationsForDay).replace(/"/g, "'")})">${d}</td>`;
                            }

                            if(d == new Date().getDate() && date.getMonth() == new Date().getMonth() && date.getFullYear() == new Date().getFullYear()) {
                                LecturerCalendarInAccount.selectDay(date, reservationsForDay);
                            }

                            d++;
                        }
                    }
                }).then(() => {
                calendar.querySelector("table").style.display = "table";
                if (loadingDiv) loadingDiv.style.display = "none";
                Temp.LecturerCalendarInAccount.loading = false;
            });
        }

        public static selectDay(date: Date, reservations: any[]): void {
            const monthText: string = Temp.LecturerCalendarInAccount.monthText + "";
            const words: string[] = monthText.split(" ");
            const month: string  = words[0];
            const year: number = parseInt(words[1]);

            if(document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent")) document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent").remove();
            document.querySelector("#lecturer-calendar .bottom #dayprops").innerHTML += `<div class="days-parent"></div>`;
            const daysDiv: HTMLElement = document.querySelector("#lecturer-calendar .bottom #dayprops .days-parent");
            const numberElement: HTMLElement = Array.from(document.querySelectorAll("#lecturer-calendar .bottom #calendar table tbody tr td")).find(td => { return new Date(year, this.getMonthNumber(month), parseInt(td.textContent)).getTime() === new Date(date).getTime() }) as HTMLElement;



            // odstranění třídy selected z předchozího vybraného dne a přidání nového
            this.resetSelectedDay();
            numberElement.classList.add("selected");



            // odstranění věcí z předchozího vybraného dne
            daysDiv.innerHTML = "";

            reservations.forEach((reservation: { [key: string]: any }) => {
                const date = new Date(reservation.date);
                const dateToday = new Date();
                const uuid = reservation.uuid ?? null;
                const time = date.toLocaleTimeString("cs-CZ", { timeZone: "Europe/Prague"}).split(":").slice(0, 2).join(":");

                const getStatus = (): string => {
                    if (reservation.user != null) return `Rezervováno: <br>&nbsp;<br>${reservation.user ?? ""}<br>${reservation.user_first_name ?? ""} ${reservation.user_last_name ?? ""}<br>${reservation.user_email ?? ""}<br>${reservation.user_mobilenumbers ?? ""}`;
                    return "Volné";
                };
                const getStyleClass = (): string => {
                    if (reservation.user != null) return "reserved-not-you";
                    return "not-reserved";
                };
                const insertButtons = (): string => {
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



            if(Array.from(document.querySelectorAll("#lecturer-calendar .bottom #dayprops .days-parent div")).length == 0) {
                daysDiv.innerHTML += `<p>Nebyly nalezeny žádné lekce na tento den.</p>`;
            }

            daysDiv.innerHTML += `<p class="add" onclick="Web.LecturerCalendarInAccount.sendCalendarActionConfirmation('reserve', '${date}', null)">Přidat termín</p>`;
        }

        public static sendCalendarActionConfirmation(type: "delete" | "reserve", datetime: string, uuid: string): void {
            if(document.querySelector("#lecturer-calendar .bottom .month-controls") && document.querySelector("#lecturer-calendar .bottom .flex")) {
                (document.querySelector("#lecturer-calendar .bottom .month-controls") as HTMLElement).style.display = "none";
                (document.querySelector("#lecturer-calendar .bottom .flex") as HTMLElement).style.display = "none";
                (document.querySelector("#lecturer-calendar .bottom .confirmation") as HTMLElement).style.display = "block";
            }



            const confirmationDiv: HTMLElement = document.querySelector("#lecturer-calendar .bottom .confirmation .center");
            let text: string;
            let inputhtml: string = "";

            if(type === "delete") {
                text = `Jste si jistý/á, že chcete <span style="text-decoration: underline">vymazat</span> rezervaci <span style="color: var(--color-main); font-weight: bolder">${datetime}</span>?`

                confirmationDiv.innerHTML = `<p>${text}</p>` + "\n" + `
                    <div class="buttons">
                        <button class="button-primary" onclick="Web.LecturerCalendarInAccount.sendCalAction('delete', 'yes', '${uuid}')">Ano</button>
                        <button class="button-secondary" onclick="Web.LecturerCalendarInAccount.sendCalAction('${type}', 'no', '${uuid}')">Ne</button>
                    </div>
                `;
            } else if(type === "reserve") {
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

        public static sendCalAction(action: "delete" | "reserve" | string, decision: "yes" | "no", uuid: string): void {
            if(decision === "no") return this.resetCalendar();

            switch (action) {
                case "delete": {
                    fetch("/api/reservations/", {
                        method: "COMPLETEDELETE", body: JSON.stringify({ uuid: uuid })
                    }).then(() => {Web.LecturerCalendarInAccount.resetCalendar()});
                } break;
            }
        }

        public static addNewReservation(date: string): void {
            const parsedDate: Date = /*new Date(*/new Date(date)/*.toLocaleString('cs-CZ', { timeZone: 'Europe/Prague' }))*/;
            const dateString: string = parsedDate.getFullYear() + "-" + (parsedDate.getMonth() + 1) + "-" + parsedDate.getDate();
            const form: any = document.getElementById('reservationForm');
            const formData: FormData = new FormData(form);
            const datetime: string = dateString + " " + formData.get("timeselect") + ":00";



            fetch("/api/reservations/", {
                method: "post", body: JSON.stringify({ date: datetime })
            }).then(() => {Web.LecturerCalendarInAccount.resetCalendar()});
        }




        private static generateICSContent(reservations: any[]): string {
            let content = `BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Moje rezervace//EN\n`;

            for (let reservation of reservations) {
                const { date, user, uuid, user_email, user_last_name, user_first_name, user_mobilenumbers } = reservation;
                if(!user || !uuid || !date) continue;
                if (new Date(date).getTime() < new Date().getTime()) continue;

                const startDate: string = new Date(date).toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '');
                const endDate: Date = new Date(date);
                endDate.setHours(endDate.getHours() + 1);
                const endDateString: string = endDate.toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '');

                content += `BEGIN:VEVENT\n`;
                content += `UID:${uuid}\n`;
                content += `DTSTART:${startDate}\n`;
                content += `DTEND:${endDateString}\n`;
                content += `SUMMARY:${user_first_name + " " + user_last_name} - Teacher Digital Agency - schůzka\n`;
                content += `DESCRIPTION:Rezervováno uživatelem: ${user}; ${user_first_name} ${user_last_name}; ${user_email}; ${user_mobilenumbers}\n`;
                if (user_email) content += `ORGANIZER;CN=${user_first_name} ${user_last_name}:mailto:${user_email}\n`;
                if (user_mobilenumbers && user_mobilenumbers.length > 0) content += `TEL;VALUE=uri:tel:${user_mobilenumbers[0]}\n`;
                content += `END:VEVENT\n`;
            }

            content += `END:VCALENDAR\n`;

            return content;
        }

        public static downloadCalendar(): void {
            fetch("/api/reservations/", { method: "get" })
                .then(response => response.json())
                .then((reservations: any[]) => {
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





    // funkce
    export function main(): void {
        Web.onScroll();
        Web.setPageHeightPadding();
    }

    export function selectActivityBlock(id: string): void {
        const el = document.getElementById(id);
        const y = el.getBoundingClientRect().top + window.scrollY - el.clientHeight - 80;
        window.scroll({
            top: y,
            behavior: 'smooth'
        })
    }

    export function activitiesRunLoadingAnimation(): void {
        const element = document.querySelector("#ACTIVITIES section");
        const loginInput: HTMLInputElement = document.querySelector("#ACTIVITIES section form input[type='text']");
        const submitInput: HTMLInputElement = document.querySelector("#ACTIVITIES section form input[type='submit']");
        const activitiesDiv: HTMLInputElement = document.querySelector("#ACTIVITIES section .activities");

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

        if(activitiesDiv) activitiesDiv.remove();

        if (loginInput) {
            setTimeout(() => {
                (loginInput as HTMLInputElement).disabled = true;
                (submitInput as HTMLInputElement).disabled = true;
            }, 10);
        } else console.warn("neeixstuje login input");
    }

    export function scrollIntoView(selector, offset = 80) {
        window.scroll(0, document.querySelector(selector).offsetTop - offset);
    }

    export function onScroll(): void {
        const header: HTMLElement = document.getElementById("HEADER");
        if (!header) return;

        const headerDesign = {
            default: { "background-color": "transparent", "position": "absolute", "backdrop-filter": "brightness(100%)", "display": "unset" },
            scrolled: { "background-color": "rgba(255,255,255,0.15)", "position": "fixed", "backdrop-filter": "brightness(10%)", "display": "unset" }
        }

        if (window.scrollY > 1) {
            for (const prop in headerDesign.scrolled) {
                header.style[prop] = headerDesign.scrolled[prop];
            }
        } else {
            for (const prop in headerDesign.default) {
                header.style[prop] = headerDesign.default[prop];
            }
        }
    }

    export function setPageHeightPadding(): void {
        const el = document.querySelector(".main");
        const y = el?.clientHeight - 100;

        if (y && y > window.screen.height) el?.classList.add("pb");
    }

    export function toggleMenu(): void {
        const menuIcon: Element = document.getElementById("HEADER").getElementsByClassName("mobile-menu-icon")[0];
        const menuDiv: HTMLElement = menuIcon.getElementsByClassName("mobile-menu")[0] as HTMLElement;

        if (menuDiv) menuDiv.style.display = menuDiv.style.display === "none" ? "block" : "none";
    }

    export function setElHeightToScrollHeight(element: HTMLElement): void {
        element.style.height = "40px";
        element.style.height = element.scrollHeight + 'px';
    }

    export function accountSelectWindow(type: "calendar" | "settings"): void {
        const el: HTMLElement = document.querySelector("#ACCOUNT .window-selection");

        if(type == "calendar") {
            (document.querySelector("#ACCOUNT #lecturer-calendar") as HTMLElement).style.display = "block";
            (document.querySelector("#ACCOUNT .userinfointerface") as HTMLElement).style.display = "none";
            (document.querySelector("#ACCOUNT .exportcal") as HTMLElement).style.display = "block";
        } else if (type == "settings") {
            (document.querySelector("#ACCOUNT #lecturer-calendar") as HTMLElement).style.display = "none";
            (document.querySelector("#ACCOUNT .exportcal") as HTMLElement).style.display = "none";
            (document.querySelector("#ACCOUNT .userinfointerface") as HTMLElement).style.display = "flex";
        }
    }

    export async function getIfUserIsLoggedIn(): Promise<boolean> {
        const response = await fetch("/api/user_is_logged");
        return await response.json();
    }
}






// region eventy
document.addEventListener("scroll", (event) => {
    Web.onScroll();
});

window.onload = () => {
    if(document.querySelector("#ACCOUNT")) Web.LecturerCalendarInAccount.resetCalendar();
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
if (targetNode) observer.observe(targetNode, config);



const targetNode1 = document.getElementById("lecturercalendar-monthcontrols-maintext");
const observer1 = new MutationObserver((mutationsList, observer) => {
    Web.LecturerCalendarInAccount.render();
});

const config1 = { childList: true, subtree: true };
if (targetNode1) observer1.observe(targetNode1, config1);
// endregion