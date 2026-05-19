import 'package:flutter/material.dart';

import '../services/session_service.dart';

import '../pages/auth/loginPage.dart';

import '../pages/guru/dashboardGuru.dart';
import '../pages/guru/jadwalGuru.dart';
import '../pages/guru/absensiGuru.dart';
import '../pages/guru/elearningGuru.dart';

class GuruDrawer extends StatelessWidget {
  final int selectedIndex;

  const GuruDrawer({
    super.key,
    required this.selectedIndex,
  });

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Color(0xFF1565C0),
              Color(0xFF42A5F5),
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            const DrawerHeader(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircleAvatar(
                    radius: 35,
                    backgroundColor: Colors.white,
                    child: Icon(
                      Icons.person,
                      size: 40,
                      color: Colors.blue,
                    ),
                  ),
                  SizedBox(height: 10),
                  Text(
                    "Guru",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                    ),
                  ),
                ],
              ),
            ),

            _buildMenu(
              context,
              icon: Icons.dashboard,
              title: "Dashboard",
              page: const DashboardGuru(),
              index: 0,
            ),

            _buildMenu(
              context,
              icon: Icons.calendar_month,
              title: "Jadwal",
              page: const JadwalGuru(),
              index: 1,
            ),

            _buildMenu(
              context,
              icon: Icons.fact_check,
              title: "Absensi",
              page: const AbsensiGuru(),
              index: 2,
            ),

            _buildMenu(
              context,
              icon: Icons.school,
              title: "E-Learning",
              page: const ElearningGuru(),
              index: 3,
            ),

            // LOGOUT
            Padding(

              padding: const EdgeInsets.symmetric(
                horizontal: 10,
              ),

              child: Container(

                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  color: Colors.redAccent,
                ),

                child: ListTile(

                  leading: const Icon(
                    Icons.logout,
                    color: Colors.white,
                  ),

                  title: const Text(

                    "Logout",

                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  onTap: () {

                    // HAPUS SESSION
                    SessionService.logout();

                    // KEMBALI KE LOGIN
                    Navigator.pushAndRemoveUntil(

                      context,

                      MaterialPageRoute(
                        builder: (_) => const LoginPage(),
                      ),

                      (route) => false,
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenu(
    BuildContext context, {
    required IconData icon,
    required String title,
    required Widget page,
    required int index,
  }) {
    bool isSelected = selectedIndex == index;

    return Padding(
      padding: const EdgeInsets.symmetric(
        horizontal: 10,
        vertical: 5,
      ),
      child: Container(
        decoration: BoxDecoration(
          color:
              isSelected ? Colors.white : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
        ),
        child: ListTile(
          leading: Icon(
            icon,
            color:
                isSelected ? Colors.blue : Colors.white,
          ),
          title: Text(
            title,
            style: TextStyle(
              color:
                  isSelected ? Colors.blue : Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
          onTap: () {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(
                builder: (_) => page,
              ),
            );
          },
        ),
      ),
    );
  }
}
