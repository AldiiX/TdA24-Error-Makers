<?php

class Modals {

    public static function insertModalBlurElement(): void {
        echo "
            <div id='modalblur' onclick='Web.Modals.openModal(null)'></div>
        ";
    }

    /*public static function insertLecturerCalendar(): void {
        echo "
            <div id='modal-lecturercalendar' class='modal'>
                <div class='top'>
                    <div class='closeicon'></div>
                    <p>Rezervace učitele</p>
                </div>
            </div>
        ";
    }*/
}