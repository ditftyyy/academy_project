import 'dart:async';

import 'package:flutter/material.dart';

import '../../services/session_service.dart';

import '../../utils/routes.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() =>
      _SplashPageState();
}

class _SplashPageState
    extends State<SplashPage> {

  @override
  void initState() {

    super.initState();

    _checkLogin();
  }

  // =========================
  // CHECK LOGIN
  // =========================

  void _checkLogin() async {

    // LOAD SESSION
    await SessionService.loadUser();

    // DELAY SPLASH
    Timer(

      const Duration(seconds: 2),

      () {

        // AMBIL USER
        final user =
            SessionService.getUser();

        // JIKA WIDGET SUDAH HILANG
        if (!mounted) return;

        // =====================
        // SUDAH LOGIN
        // =====================

        if (user != null) {

          // ROLE GURU
          if (user.role ==
              "guru") {

            Navigator
                .pushReplacementNamed(

              context,

              AppRoutes.dashboardGuru,
            );
          }

          // ROLE SISWA
          else if (user.role ==
              "siswa") {

            Navigator
                .pushReplacementNamed(

              context,

              AppRoutes.dashboardSiswa,
            );
          }

          // ROLE TIDAK VALID
          else {

            Navigator
                .pushReplacementNamed(

              context,

              AppRoutes.login,
            );
          }
        }

        // =====================
        // BELUM LOGIN
        // =====================

        else {

          Navigator
              .pushReplacementNamed(

            context,

            AppRoutes.login,
          );
        }
      },
    );
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      body: Container(

        width: double.infinity,

        height: double.infinity,

        decoration:
            const BoxDecoration(

          gradient: LinearGradient(

            begin:
                Alignment.topLeft,

            end:
                Alignment
                    .bottomRight,

            colors: [

              Color(0xFF1565C0),

              Color(0xFF42A5F5),

              Color(0xFF90CAF9),
            ],
          ),
        ),

        child: const Center(

          child: Column(

            mainAxisAlignment:
                MainAxisAlignment.center,

            children: [

              // ICON
              Icon(

                Icons.school,

                size: 100,

                color: Colors.white,
              ),

              SizedBox(height: 20),

              // TITLE
              Text(

                "Academy+",

                style: TextStyle(

                  fontSize: 32,

                  color: Colors.white,

                  fontWeight:
                      FontWeight.bold,
                ),
              ),

              SizedBox(height: 40),

              // LOADING
              CircularProgressIndicator(

                color: Colors.white,
              ),
            ],
          ),
        ),
      ),
    );
  }
}