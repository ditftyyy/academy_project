import 'package:flutter/material.dart';

import 'guruDrawer.dart';

class BaseLayoutGuru extends StatelessWidget {

  final String title;

  final Widget body;

  final int selectedIndex;

  const BaseLayoutGuru({

    super.key,

    required this.title,
    required this.body,
    required this.selectedIndex,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        centerTitle: true,
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),

      drawer: GuruDrawer(
        selectedIndex: selectedIndex,
      ),

      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color(0xFFE3F2FD),
              Color(0xFFBBDEFB),
            ],
          ),
        ),

        child: body,
      ),
    );
  }
}