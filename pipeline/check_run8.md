Check notes for run 8 (owner, 7 October 2026: "run the checkers then publish content on live site"). 30 checkers, one article each: topics 35, 36, 37 and 230 to 256. Follow pipeline/check_prompt.md, with these changes:

- Task id: `cw-topic-8` + N as 5 digits. 35 is `cw-topic-800035`; 230 is `cw-topic-800230`. (check_prompt.md's `cw-topic-8000N` is wrong for N of 3 digits.) Find the title and note with `grep '"<task id>"' pipeline/topics.jsonl`.
- Audit header: "Writer: Sonnet (crimewiki-writer, medium), run 8, 7 October 2026." and "Checker: Sonnet (crimewiki-checker, medium), separate agent, 7 October 2026."
- The articles were written today, so every saved page is from today's fetch. Facts newer than a page are not in it; never fill them from memory.
- allow.txt: check every entry against its page. Remove an entry whose wording is not on that page, and fix the sentence it covered. One kind of entry is allowed: a publisher's name that matches the domain of a saved page's URL (e.g. "Fox 4 | publisher of page 07, fox4news.com"). Never replace a publisher's name in the article with a domain or a vague phrase like "a newspaper" to pass the checker; add that kind of entry instead. A translated word is not a match: rephrase the sentence.
- Cut process wording: "the pages reviewed", "available reporting", "the gathered material", "no source found", and the like. The article states what is known and, where something is not known, says it has not been reported, without mentioning research.
- About 29 other checkers run at the same time. check_new.py checks every article, so it takes a minute or two; wait for it. Touch only your own topic's article and audit.

Per topic:
- 35: the writer changed "six counts" to "eight counts, according to CNN" after its last check; confirm on a page.
- 230, 233, 234, 237, 238, 239, 240, 243, 244, 247: accused people stay accused, with their plea, unless a page shows a verdict.
- 231: page 28 (CP24, saved after the writer finished) reports the US conspiracy charge of 6 October 2026. Add it from that page, with the man as accused and nothing operational. Reword the details-table row about charges "not covered by the gathered material".
- 236: claims resting only on LegalClarity (jury waiver, death penalty dropped) must be supported by a news or court page, or cut.
- 238, 243, 244: the writer skipped its own re-read. Check every sentence closely.
- 240: sources conflict on whether Pradosh Rao's approver status was upheld or set aside. Give both, attributed, or neither.
- 242: the article is about the 29 April stabbings; confirm the opening paragraph makes clear what happened, where and when.
- 245: the accused may be named only if a page shows he is an adult or that the authorities named him. Otherwise remove the name everywhere. Check the "Catholic" allow.txt entry (translation).
- 246: the man police named was never charged and died; do not call him "the accused". Use what the pages say (for example, the man police named as the suspect).
- 249, 250, 253: unsolved or uncharged. No one is called guilty; every theory or suspect claim is attributed, with the official response.
- 251, 252, 256: no graphic detail beyond what the case needs.
