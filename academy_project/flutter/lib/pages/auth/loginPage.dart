import 'package:flutter/material.dart';

import '../../utils/routes.dart';

// import '../../services/api_service.dart';
import '../../services/auth_service.dart';
import '../../models/user_model.dart';
import '../../services/session_service.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() =>
      _LoginPageState();
}

class _LoginPageState
    extends State<LoginPage> {

  final _formKey =
      GlobalKey<FormState>();

  final _usernameController =
      TextEditingController();

  final _passwordController =
      TextEditingController();

  bool isPasswordHidden = true;

  final AuthService _authService =
      AuthService();

  @override
  void dispose() {

    _usernameController.dispose();

    _passwordController.dispose();

    super.dispose();
  }

  // =========================
  // HANDLE LOGIN
  // =========================

void _handleLogin() async {

  if (_formKey.currentState!
      .validate()) {

    String username =
        _usernameController.text
            .trim();

    String password =
        _passwordController.text
            .trim();

    // LOGIN DUMMY
    UserModel? user =
        _authService.login(

      username: username,

      password: password,
    );

    // LOGIN BERHASIL
    if (user != null) {

      await SessionService.setUser(
        user,
      );

      // ROLE GURU
      if (user.role == "guru") {

        Navigator.pushReplacementNamed(

          context,

          AppRoutes.dashboardGuru,
        );
      }

      // ROLE SISWA
      else {

        Navigator.pushReplacementNamed(

          context,

          AppRoutes.dashboardSiswa,
        );
      }
    }

    // LOGIN GAGAL
    else {

      ScaffoldMessenger.of(context)
          .showSnackBar(

        const SnackBar(

          content: Text(
            "Username atau password salah",
          ),
        ),
      );
    }
  }
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

        child: SafeArea(

          child: Center(

            child:
                SingleChildScrollView(

              padding:
                  const EdgeInsets
                      .all(24),

              child: Card(

                elevation: 10,

                shape:
                    RoundedRectangleBorder(

                  borderRadius:
                      BorderRadius
                          .circular(25),
                ),

                child: Padding(

                  padding:
                      const EdgeInsets
                          .all(24),

                  child: Form(

                    key: _formKey,

                    child: Column(

                      mainAxisSize:
                          MainAxisSize.min,

                      children: [

                        // ICON
                        const CircleAvatar(

                          radius: 45,

                          backgroundColor:
                              Colors.blue,

                          child: Icon(

                            Icons.school,

                            size: 50,

                            color:
                                Colors.white,
                          ),
                        ),

                        const SizedBox(
                          height: 20,
                        ),

                        // TITLE
                        const Text(

                          "Academy+",

                          style:
                              TextStyle(

                            fontSize: 32,

                            fontWeight:
                                FontWeight
                                    .bold,

                            color:
                                Colors.blue,
                          ),
                        ),

                        const SizedBox(
                          height: 10,
                        ),

                        const Text(

                          "Login",

                          style:
                              TextStyle(

                            color:
                                Colors.grey,

                            fontSize: 16,
                          ),
                        ),

                        const SizedBox(
                          height: 35,
                        ),

                        // USERNAME
                        TextFormField(

                          controller:
                              _usernameController,

                          decoration:
                              InputDecoration(

                            labelText:
                                "Username",

                            hintText:
                                "Masukkan Username",

                            prefixIcon:
                                const Icon(
                              Icons.person,
                            ),

                            border:
                                OutlineInputBorder(

                              borderRadius:
                                  BorderRadius.circular(
                                15,
                              ),
                            ),
                          ),

                          validator:
                              (value) {

                            if (value ==
                                    null ||
                                value
                                    .isEmpty) {

                              return
                                  "Username wajib diisi";
                            }

                            return null;
                          },
                        ),

                        const SizedBox(
                          height: 20,
                        ),

                        // PASSWORD
                        TextFormField(

                          controller:
                              _passwordController,

                          obscureText:
                              isPasswordHidden,

                          decoration:
                              InputDecoration(

                            labelText:
                                "Password",

                            hintText:
                                "Masukkan Password",

                            prefixIcon:
                                const Icon(
                              Icons.lock,
                            ),

                            suffixIcon:
                                IconButton(

                              icon: Icon(

                                isPasswordHidden

                                    ? Icons
                                        .visibility_off

                                    : Icons
                                        .visibility,
                              ),

                              onPressed: () {

                                setState(() {

                                  isPasswordHidden =
                                      !isPasswordHidden;
                                });
                              },
                            ),

                            border:
                                OutlineInputBorder(

                              borderRadius:
                                  BorderRadius.circular(
                                15,
                              ),
                            ),
                          ),

                          validator:
                              (value) {

                            if (value ==
                                    null ||
                                value
                                    .isEmpty) {

                              return
                                  "Password wajib diisi";
                            }

                            if (value.length <
                                6) {

                              return
                                  "Password minimal 6 karakter";
                            }

                            return null;
                          },
                        ),

                        const SizedBox(
                          height: 30,
                        ),

                        // BUTTON LOGIN
                        SizedBox(

                          width:
                              double.infinity,

                          child:
                              ElevatedButton(

                            onPressed:
                                _handleLogin,

                            style:
                                ElevatedButton.styleFrom(

                              backgroundColor:
                                  Colors.blue,

                              foregroundColor:
                                  Colors.white,

                              padding:
                                  const EdgeInsets.symmetric(

                                vertical: 16,
                              ),

                              shape:
                                  RoundedRectangleBorder(

                                borderRadius:
                                    BorderRadius.circular(
                                  15,
                                ),
                              ),
                            ),

                            child:
                                const Text(

                              "Login",

                              style:
                                  TextStyle(

                                fontSize: 18,

                                fontWeight:
                                    FontWeight.bold,
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(
                          height: 20,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}