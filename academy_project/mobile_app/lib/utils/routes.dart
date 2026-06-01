import 'package:flutter/material.dart';

// =========================
// AUTH
// =========================
import '../pages/auth/loginPage.dart';
import '../pages/auth/splashPage.dart';

// =========================
// SISWA
// =========================
import '../pages/siswa/dashboardSiswa.dart';
import '../pages/siswa/elearningSiswa.dart';
import '../pages/siswa/absensiSiswa.dart';
import '../pages/siswa/jadwalSiswa.dart';
// detail pages imported where needed

// =========================
// GURU
// =========================
import '../pages/guru/dashboardGuru.dart';
import '../pages/guru/elearningGuru.dart';
import '../pages/guru/absensiGuru.dart';
import '../pages/guru/jadwalGuru.dart';
// detail pages imported where needed

class AppRoutes {
  // =========================
  // AUTH
  // =========================

  static const String splash = "/splash";

  static const String login = "/login";

  // =========================
  // SISWA
  // =========================

  static const String dashboardSiswa = "/dashboardSiswa";

  static const String elearningSiswa = "/elearningSiswa";

  static const String absensiSiswa = "/absensiSiswa";

  static const String jadwalSiswa = "/jadwalSiswa";

  // =========================
  // GURU
  // =========================

  static const String dashboardGuru = "/dashboardGuru";

  static const String elearningGuru = "/elearningGuru";

  static const String absensiGuru = "/absensiGuru";

  static const String jadwalGuru = "/jadwalGuru";

  // =========================
  // GENERATE ROUTE
  // =========================

  static Route<dynamic> generateRoute(RouteSettings settings) {
    switch (settings.name) {
      // =========================
      // AUTH
      // =========================

      case splash:
        return MaterialPageRoute(builder: (_) => const SplashPage());

      case login:
        return MaterialPageRoute(builder: (_) => const LoginPage());

      // =========================
      // SISWA
      // =========================

      case dashboardSiswa:
        return MaterialPageRoute(builder: (_) => const DashboardSiswa());

      case elearningSiswa:
        return MaterialPageRoute(builder: (_) => const ElearningSiswa());

      case absensiSiswa:
        return MaterialPageRoute(builder: (_) => const AbsensiSiswa());

      case jadwalSiswa:
        return MaterialPageRoute(builder: (_) => const JadwalSiswa());

      // =========================
      // GURU
      // =========================

      case dashboardGuru:
        return MaterialPageRoute(builder: (_) => const DashboardGuru());

      case elearningGuru:
        return MaterialPageRoute(builder: (_) => const ElearningGuru());

      case absensiGuru:
        return MaterialPageRoute(builder: (_) => const AbsensiGuru());

      case jadwalGuru:
        return MaterialPageRoute(builder: (_) => const JadwalGuru());

      // =========================
      // DEFAULT
      // =========================

      default:
        return MaterialPageRoute(builder: (_) => const SplashPage());
    }
  }
}
