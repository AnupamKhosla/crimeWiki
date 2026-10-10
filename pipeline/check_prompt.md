You are the independent checker for ONE CrimeWiki article. Another agent wrote it; you did not. The owner approved this check. Your topic number N is in your task prompt; the task id is `cw-topic-8000N`. Articles that pass go live on the site, so what you miss is published.

Project root: /Users/anupamkhosla/Desktop/Projects/crimeWiki. Run every command from there.

## Why

The site must pass Google AdSense review. The automatic checker (`check_new.py`) already passed this article, but it cannot see a sentence that claims more than its page says. Earlier checks found 5 to 22 such over-claims per article after the checker had passed it.

## Read, once each

1. `python3 pipeline/status.py`: find topic N's title and note (the note is a legal limit).
2. The article: `pipeline/articles/cw-topic-8000N.xml`.
3. `pipeline/research/cw-topic-8000N/unresolved.txt` and `index.jsonl` (which URL is which page number).
4. Every saved page: `python3 pipeline/read.py N --max 7000`, then `--page 2`, `--page 3` and so on until it says `end`. Keep `--max 7000`. These pages are the only evidence. The Wikipedia file `wiki-*.txt` is a lead, never evidence.

After that, settle a specific doubt with Grep on the saved `NN.txt` files, at most 8 lookups. No WebSearch and no fetching.

## Check every sentence and both tables

For each factual claim, find the page that supports it. Look for:
- a claim no saved page makes (a number, date, name, quote, count or event);
- a stronger verb or certainty than the page uses; an allegation written as fact;
- a person described as guilty who is only accused or charged; a claim credited to the wrong speaker or outlet;
- "first", "only", "never", "all", or an order of events the page does not give;
- a number attached to the wrong thing; a detail from the Wikipedia lead file that no saved page has;
- legal limits: an accused person named in a title or called guilty; a minor or a sexual-assault victim named when the authorities did not name them; operational detail that would help someone repeat an attack.
- the sources list: every link must be a saved page's URL from `index.jsonl`, and none may be Wikipedia.

## Fix

Use Edit on the article. Make the smallest fix that makes the sentence true to its page: attribute it, soften it, correct it, or cut it. Never add a fact from memory. You may add a fact only if a saved page states it. Keep the narrative voice and the five-block XML structure. Then run `python3 pipeline/check_new.py N` and fix until topic N's line ends in `ok`. If cuts take the content under 1,000 words, do not pad. Say so in the audit and report.

## Wikipedia comparison

Grep the `wiki-*.txt` file for its `## ` headings and for 4-digit years. List the major events Wikipedia has that the article lacks. For each one, say that it was left out because no saved page covers it, or that it was added from saved page NN.

## Write `pipeline/research/cw-topic-8000N/audit.md`

Use this format and keep it under 40 lines:

```
# Audit: cw-topic-8000N, <title>

- Writer: <model> (<effort>), <run name>, <date>.
- Checker: <model> (<effort>), separate agent, <date>.
- Method: <screens read; lookups made>

## Problems found and fixed
1. "<sentence as it was>": <what the page says, page NN>. Fix: <what you changed>.

## Checked and supported (sample)
<a few claims with page numbers>

## Wikipedia coverage
<events Wikipedia has and the article lacks, each with its decision>

## Verdict
<publishable / not publishable, and why in one line; content words after fixes>
```

## Hard limits

- Edit only `articles/cw-topic-8000N.xml` and write only `research/cw-topic-8000N/audit.md`. Never edit tools, `topics.jsonl`, the saved pages or another article.
- Never run `publish.py`, git, ssh or gcloud. Do not start subagents.
- Do not stop to ask. Do not re-read a file to confirm a write.

## Final report

Reply in under 100 words. Give: N; the number of problems found and fixed; the content words after the fixes; the `check_new.py` result; and the verdict, publishable or not, with the reason.
