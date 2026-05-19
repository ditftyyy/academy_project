import 'package:flutter/material.dart';

import 'package:video_player/video_player.dart';

class VideoPlayerPage
    extends StatefulWidget {

  final String title;

  final String videoPath;

  const VideoPlayerPage({

    super.key,

    required this.title,

    required this.videoPath,
  });

  @override
  State<VideoPlayerPage>
      createState() =>
          _VideoPlayerPageState();
}

class _VideoPlayerPageState
    extends State<VideoPlayerPage> {

  late VideoPlayerController
      controller;

  @override
  void initState() {

    super.initState();

    controller =
        VideoPlayerController.asset(
      widget.videoPath,
    )
          ..initialize().then((_) {

            setState(() {});
          });
  }

  @override
  void dispose() {

    controller.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: Text(widget.title),

        backgroundColor:
            Colors.green,
      ),

      body: Center(

        child:
            controller.value.isInitialized

                ? Column(

                    mainAxisAlignment:
                        MainAxisAlignment
                            .center,

                    children: [

                      AspectRatio(

                        aspectRatio:
                            controller
                                .value
                                .aspectRatio,

                        child: VideoPlayer(
                          controller,
                        ),
                      ),

                      const SizedBox(
                        height: 20,
                      ),

                      ElevatedButton.icon(

                        onPressed: () {

                          setState(() {

                            controller
                                    .value
                                    .isPlaying

                                ? controller
                                    .pause()

                                : controller
                                    .play();
                          });
                        },

                        icon: Icon(

                          controller
                                  .value
                                  .isPlaying

                              ? Icons.pause

                              : Icons.play_arrow,
                        ),

                        label: Text(

                          controller
                                  .value
                                  .isPlaying

                              ? "Pause"

                              : "Play",
                        ),
                      ),
                    ],
                  )

                : const CircularProgressIndicator(),
      ),
    );
  }
}