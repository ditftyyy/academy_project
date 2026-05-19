import 'package:flutter/material.dart';

import 'utils/routes.dart';
import 'theme/app_theme.dart';

void main() {

  runApp(
    const MyApp(),
  );
}

class MyApp extends StatelessWidget {

  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {

    return MaterialApp(

      debugShowCheckedModeBanner:
          false,

      title: "Academy+",

      theme: AppTheme.lightTheme,

      initialRoute:
          AppRoutes.splash,

      onGenerateRoute:
          AppRoutes.generateRoute,
    );
  }
}