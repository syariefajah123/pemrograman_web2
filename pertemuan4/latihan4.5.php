   Hari ini :
    <?php
    $nama_hari = date("l");

    switch ($nama_hari) {
        case "Sunday":
            print("Minggu");
            print "Waktu untuk istirahat";
            break;
        case "Monday":
            print("Senin <br>");
            print "Meeting awal minggu jam 08.00";
            break;
        case "Tuesday":
            print("Selasa <br>");
            print "Pembukaan Workshop Diklat";
            break;
        case "Wednesday":
            print("Rabu <br>");
            print "Seminar Launching Windows 8";
            break;
        case "Thursday":
            print("Kamis <br>");
            print "Workshop Web Programming";
            break;
        case "Friday":
            print("Jumat <br>");
            print "Sholat Jumat";
            break;
        case "Saturday":
            print("Sabtu <br>");
            print "Kunjungan Industri";
            break;
        default:
            print("Hari tidak ditemukan");
    }
    ?>