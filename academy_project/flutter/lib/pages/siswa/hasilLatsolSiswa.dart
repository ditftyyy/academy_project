import 'package:flutter/material.dart';

class HasilLatsolSiswa
    extends StatelessWidget {

  final int score;

  final int total;

  final List<Map<String, dynamic>>
      questions;

  final List<int?> answers;

  const HasilLatsolSiswa({

    super.key,

    required this.score,

    required this.total,

    required this.questions,

    required this.answers,
  });

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: const Text(
          "Hasil Quiz",
        ),

        backgroundColor:
            Colors.green,
      ),

      body: Padding(

        padding:
            const EdgeInsets.all(20),

        child: Column(

          children: [

            Container(

              width: double.infinity,

              padding:
                  const EdgeInsets.all(
                25,
              ),

              decoration: BoxDecoration(

                color:
                    Colors.green
                        .withOpacity(0.1),

                borderRadius:
                    BorderRadius.circular(
                  25,
                ),
              ),

              child: Column(

                children: [

                  const Icon(

                    Icons.emoji_events,

                    color: Colors.green,

                    size: 80,
                  ),

                  const SizedBox(
                    height: 20,
                  ),

                  Text(

                    "$score / $total",

                    style:
                        const TextStyle(

                      fontSize: 40,

                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const SizedBox(
                    height: 10,
                  ),

                  const Text(

                    "Skor Quiz Kamu",

                    style: TextStyle(
                      fontSize: 18,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            SizedBox(

              width: double.infinity,

              child: ElevatedButton(

                onPressed: () {

                  showModalBottomSheet(

                    context: context,

                    isScrollControlled:
                        true,

                    builder: (_) {

                      return Padding(

                        padding:
                            const EdgeInsets.all(
                          20,
                        ),

                        child: ListView.builder(

                          itemCount:
                              questions.length,

                          itemBuilder:
                              (_, index) {

                            final q =
                                questions[index];

                            bool correct =
                                answers[index] ==
                                    q["answer"];

                            return Container(

                              margin:
                                  const EdgeInsets.only(
                                bottom: 20,
                              ),

                              padding:
                                  const EdgeInsets.all(
                                15,
                              ),

                              decoration:
                                  BoxDecoration(

                                color:
                                    Colors.white,

                                borderRadius:
                                    BorderRadius.circular(
                                  20,
                                ),
                              ),

                              child: Column(

                                crossAxisAlignment:
                                    CrossAxisAlignment.start,

                                children: [

                                  Text(

                                    "${index + 1}. ${q["question"]}",

                                    style:
                                        const TextStyle(

                                      fontWeight:
                                          FontWeight.bold,
                                    ),
                                  ),

                                  const SizedBox(
                                    height: 10,
                                  ),

                                  Text(

                                    "Jawaban benar: ${q["options"][q["answer"]]}",

                                    style: const TextStyle(
                                      color:
                                          Colors.green,
                                    ),
                                  ),

                                  const SizedBox(
                                    height: 5,
                                  ),

                                  Text(

                                    correct
                                        ? "Jawaban kamu benar"
                                        : "Jawaban kamu salah",

                                    style: TextStyle(

                                      color:
                                          correct

                                              ? Colors.green

                                              : Colors.red,
                                    ),
                                  ),

                                  const SizedBox(
                                    height: 10,
                                  ),

                                  Text(
                                    q["explanation"],
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      );
                    },
                  );
                },

                style:
                    ElevatedButton.styleFrom(

                  backgroundColor:
                      Colors.blue,

                  foregroundColor:
                      Colors.white,

                  padding:
                      const EdgeInsets.symmetric(
                    vertical: 15,
                  ),
                ),

                child: const Text(
                  "Lihat Pembahasan",
                ),
              ),
            ),

            const SizedBox(height: 15),

            SizedBox(

              width: double.infinity,

              child: ElevatedButton(

                onPressed: () {

                  Navigator.pop(context);
                },

                style:
                    ElevatedButton.styleFrom(

                  backgroundColor:
                      Colors.grey,

                  foregroundColor:
                      Colors.white,

                  padding:
                      const EdgeInsets.symmetric(
                    vertical: 15,
                  ),
                ),

                child: const Text(
                  "Kembali",
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
