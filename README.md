# The Imitation Game

A dead simple Captcha implementation, because I couldn't find a good one.

It thinks of a question and an answer, then it hashes the answer with the current time and page ID, and some random salt. That means any given hash/answer combination is good for one page or one blog, for three hours (by default). Obviously you can do a small spam attack with that, but you can't totally automate it. For any small blog, that should be enough to put attackers off.

At present, the in-built challenges are trivially easy, so you probably want to change that.

All challenge answers are currently case-sensitive.

## Getting started:
* Copy into the plugins directory and enable as normal.
* Edit the `generateChallenge` function if you want custom questions.
* Edit the `showCaptcha` function if you want to change the generated DOM.
* Edit the `answerIsRight` function if you want to change the lifetime of a challenge.