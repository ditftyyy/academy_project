import 'package:flutter/material.dart';

import 'hasilLatsolSiswa.dart';

class LatsolSiswa extends StatefulWidget {

  const LatsolSiswa({super.key});

  @override
  State<LatsolSiswa> createState() =>
      _LatsolSiswaState();
}

class _LatsolSiswaState
    extends State<LatsolSiswa> {

  int currentQuestion = 0;

  List<int?> answers = [];

  List<bool> marked = [];

  final List<Map<String, dynamic>>
      questions = [

    {

      "question":
          "Berapa hasil 5 × 5?",

      "options": [

        "10",

        "20",

        "25",

        "30",
      ],

      "answer": 2,

      "explanation":
          "Karena 5 × 5 = 25",
    },

    {

      "question":
          "Nilai x dari persamaan linier  7x+23=4x-1 adalah …",

      "options": [

        "1",

        "-1",

        "0",

        "3",
      ],

      "answer": 1,

      "explanation":
          "7x+23=4x-1\n7x+2=3(4x-1)\n7x+2=12x-3\n7x-12x=-3-2\n-5x=-5",
    },

    {

      "question":
          "Planet terbesar?",

      "options": [

        "Mars",

        "Venus",

        "Jupiter",

        "Bumi",
      ],

      "answer": 2,

      "explanation":
          "Planet terbesar adalah Jupiter.",
    },
  ];

  @override
  void initState() {

    super.initState();

    answers =
        List.filled(
      questions.length,
      null,
    );

    marked =
        List.filled(
      questions.length,
      false,
    );
  }

  @override
  Widget build(BuildContext context) {

    final question =
        questions[currentQuestion];

    return Scaffold(

      appBar: AppBar(

        title:
            const Text("Quiz"),

        backgroundColor:
            Colors.green,

        actions: [

          IconButton(

            onPressed: () {

              _showQuestionGrid();
            },

            icon: const Icon(
              Icons.grid_view,
            ),
          ),
        ],
      ),

      body: Padding(

        padding:
            const EdgeInsets.all(20),

        child: Column(

          crossAxisAlignment:
              CrossAxisAlignment.start,

          children: [

            Row(

              children: [

                Text(

                  "Soal ${currentQuestion + 1}",

                  style: const TextStyle(

                    fontSize: 18,

                    color: Colors.grey,
                  ),
                ),

                const SizedBox(width: 15),

                if (marked[
                    currentQuestion])

                  Container(

                    padding:
                        const EdgeInsets.symmetric(

                      horizontal: 12,

                      vertical: 5,
                    ),

                    decoration:
                        BoxDecoration(

                      color:
                          Colors.orange,

                      borderRadius:
                          BorderRadius.circular(
                        20,
                      ),
                    ),

                    child: const Text(

                      "Ditandai",

                      style: TextStyle(
                        color: Colors.white,
                      ),
                    ),
                  ),
              ],
            ),

            const SizedBox(height: 20),

            Text(

              question["question"],

              style: const TextStyle(

                fontSize: 24,

                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(height: 30),

            // =========================
            // OPTIONS
            // =========================

            ...List.generate(

              question["options"].length,

              (index) {

                bool selected =
                    answers[
                            currentQuestion] ==
                        index;

                return GestureDetector(

                  onTap: () {

                    setState(() {

                      answers[
                              currentQuestion] =
                          index;
                    });
                  },

                  child: Container(

                    width:
                        double.infinity,

                    margin:
                        const EdgeInsets.only(
                      bottom: 15,
                    ),

                    padding:
                        const EdgeInsets.symmetric(

                      horizontal: 20,

                      vertical: 18,
                    ),

                    decoration: BoxDecoration(

                      color:

                          selected

                              ? Colors.green

                              : Colors.white,

                      borderRadius:
                          BorderRadius.circular(
                        18,
                      ),

                      border: Border.all(

                        color:

                            selected

                                ? Colors.green

                                : Colors.grey
                                    .shade300,

                        width: 2,
                      ),

                      boxShadow: [

                        BoxShadow(

                          color:
                              Colors.black12,

                          blurRadius: 4,

                          offset:
                              const Offset(
                            0,
                            2,
                          ),
                        ),
                      ],
                    ),

                    child: Text(

                      question["options"]
                          [index],

                      style: TextStyle(

                        fontSize: 17,

                        color:

                            selected

                                ? Colors.white

                                : Colors.black,

                        fontWeight:
                            FontWeight.w500,
                      ),
                    ),
                  ),
                );
              },
            ),

            const SizedBox(height: 10),

            // =========================
            // BUTTON TANDAI
            // =========================

            SizedBox(

              width: double.infinity,

              child: ElevatedButton.icon(

                onPressed: () {

                  setState(() {

                    marked[
                            currentQuestion] =
                        !marked[
                            currentQuestion];
                  });
                },

                icon: Icon(

                  marked[currentQuestion]

                      ? Icons.bookmark

                      : Icons.bookmark_border,
                ),

                label: Text(

                  marked[currentQuestion]

                      ? "Tandai Soal"

                      : "Tandai Soal",
                ),

                style:
                    ElevatedButton.styleFrom(

                  backgroundColor:
                      Colors.orange,

                  foregroundColor:
                      Colors.white,

                  padding:
                      const EdgeInsets.symmetric(
                    vertical: 15,
                  ),
                ),
              ),
            ),

            const Spacer(),

            // =========================
            // NAVIGATION
            // =========================

            Row(

              children: [

                Expanded(

                  child: ElevatedButton(

                    onPressed:
                        currentQuestion == 0

                            ? null

                            : () {

                                setState(() {

                                  currentQuestion--;
                                });
                              },

                    child: const Text(
                      "Previous",
                    ),
                  ),
                ),

                const SizedBox(width: 15),

                Expanded(

                  child: ElevatedButton(

                    onPressed: () {

                      // NEXT
                      if (currentQuestion <
                          questions.length -
                              1) {

                        setState(() {

                          currentQuestion++;
                        });
                      }

                      // REVIEW
                      else {

                        _showReviewPage();
                      }
                    },

                    style:
                        ElevatedButton.styleFrom(

                      backgroundColor:
                          Colors.green,

                      foregroundColor:
                          Colors.white,
                    ),

                    child: Text(

                      currentQuestion ==
                              questions
                                      .length -
                                  1

                          ? "Selesai"

                          : "Next",
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // =========================
  // GRID NOMOR SOAL
  // =========================

  void _showQuestionGrid() {

    showModalBottomSheet(

      context: context,

      builder: (_) {

        return Padding(

          padding:
              const EdgeInsets.all(
            20,
          ),

          child: GridView.builder(

            shrinkWrap: true,

            itemCount:
                questions.length,

            gridDelegate:
                const SliverGridDelegateWithFixedCrossAxisCount(

              crossAxisCount: 5,

              crossAxisSpacing: 10,

              mainAxisSpacing: 10,
            ),

            itemBuilder: (_, index) {

              Color color =
                  Colors.grey;

              if (marked[index]) {

                color = Colors.orange;
              }

              if (answers[index] !=
                  null) {

                color = Colors.green;
              }

              return GestureDetector(

                onTap: () {

                  setState(() {

                    currentQuestion =
                        index;
                  });

                  Navigator.pop(
                      context);
                },

                child: Container(

                  decoration:
                      BoxDecoration(

                    color: color,

                    borderRadius:
                        BorderRadius.circular(
                      12,
                    ),
                  ),

                  child: Center(

                    child: Text(

                      "${index + 1}",

                      style:
                          const TextStyle(

                        color:
                            Colors.white,

                        fontWeight:
                            FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }

  // =========================
  // REVIEW BEFORE SUBMIT
  // =========================

  void _showReviewPage() {

    showModalBottomSheet(

      context: context,

      isScrollControlled: true,

      builder: (_) {

        return Padding(

          padding:
              const EdgeInsets.all(
            20,
          ),

          child: Column(

            mainAxisSize:
                MainAxisSize.min,

            children: [

              const Text(

                "Review Jawaban",

                style: TextStyle(

                  fontSize: 24,

                  fontWeight:
                      FontWeight.bold,
                ),
              ),

              const SizedBox(height: 20),

              GridView.builder(

                shrinkWrap: true,

                itemCount:
                    questions.length,

                gridDelegate:
                    const SliverGridDelegateWithFixedCrossAxisCount(

                  crossAxisCount: 5,

                  crossAxisSpacing:
                      10,

                  mainAxisSpacing:
                      10,
                ),

                itemBuilder: (_, index) {

                  Color color =
                      Colors.grey;

                  if (marked[index]) {

                    color =
                        Colors.orange;
                  }

                  if (answers[index] !=
                      null) {

                    color =
                        Colors.green;
                  }

                  return Container(

                    decoration:
                        BoxDecoration(

                      color: color,

                      borderRadius:
                          BorderRadius.circular(
                        12,
                      ),
                    ),

                    child: Center(

                      child: Text(

                        "${index + 1}",

                        style:
                            const TextStyle(

                          color:
                              Colors.white,

                          fontWeight:
                              FontWeight.bold,
                        ),
                      ),
                    ),
                  );
                },
              ),

              const SizedBox(height: 25),

              SizedBox(

                width: double.infinity,

                child: ElevatedButton(

                  onPressed: () {

                    int score = 0;

                    for (
                      int i = 0;
                      i <
                          questions
                              .length;
                      i++
                    ) {

                      if (answers[i] ==
                          questions[i]
                              ["answer"]) {

                        score++;
                      }
                    }

                    Navigator.pop(context);

                    Navigator.pushReplacement(

                      context,

                      MaterialPageRoute(

                        builder: (_) =>
                            HasilLatsolSiswa(

                          score: score,

                          total:
                              questions.length,

                          questions:
                              questions,

                          answers:
                              answers,
                        ),
                      ),
                    );
                  },

                  style:
                      ElevatedButton.styleFrom(

                    backgroundColor:
                        Colors.green,

                    foregroundColor:
                        Colors.white,

                    padding:
                        const EdgeInsets.symmetric(
                      vertical: 16,
                    ),
                  ),

                  child: const Text(
                    "Submit Jawaban",
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
