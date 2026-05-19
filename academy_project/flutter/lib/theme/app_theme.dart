import 'package:flutter/material.dart';

class AppTheme {

  // =========================
  // COLOR
  // =========================

  static const Color primaryColor =
      Color(0xFF1565C0);

  static const Color secondaryColor =
      Color(0xFF42A5F5);

  static const Color backgroundColor =
      Color(0xFFF5F7FA);

  static const Color whiteColor =
      Colors.white;

  static const Color greyColor =
      Colors.grey;

  static const Color dangerColor =
      Colors.red;

  static const Color successColor =
      Colors.green;

  // =========================
  // THEME
  // =========================

  static ThemeData lightTheme =
      ThemeData(

    useMaterial3: true,

    scaffoldBackgroundColor:
        backgroundColor,

    primaryColor: primaryColor,

    colorScheme:
        ColorScheme.fromSeed(

      seedColor: primaryColor,
    ),

    appBarTheme:
        const AppBarTheme(

      elevation: 0,

      centerTitle: true,

      backgroundColor:
          primaryColor,

      foregroundColor:
          whiteColor,
    ),

    elevatedButtonTheme:
        ElevatedButtonThemeData(

      style:
          ElevatedButton.styleFrom(

        backgroundColor:
            primaryColor,

        foregroundColor:
            whiteColor,

        shape:
            RoundedRectangleBorder(

          borderRadius:
              BorderRadius.circular(15),
        ),

        padding:
            const EdgeInsets.symmetric(

          vertical: 16,
        ),
      ),
    ),

    inputDecorationTheme:
        InputDecorationTheme(

      filled: true,

      fillColor: whiteColor,

      contentPadding:
          const EdgeInsets.symmetric(

        horizontal: 16,

        vertical: 16,
      ),

      border:
          OutlineInputBorder(

        borderRadius:
            BorderRadius.circular(15),

        borderSide: BorderSide.none,
      ),

      enabledBorder:
          OutlineInputBorder(

        borderRadius:
            BorderRadius.circular(15),

        borderSide:
            BorderSide.none,
      ),

      focusedBorder:
          OutlineInputBorder(

        borderRadius:
            BorderRadius.circular(15),

        borderSide:
            const BorderSide(

          color: primaryColor,

          width: 2,
        ),
      ),
    ),

    cardTheme: CardThemeData(

      elevation: 4,

      color: whiteColor,

      shape:
          RoundedRectangleBorder(

        borderRadius:
            BorderRadius.circular(20),
      ),
    ),
  );
}