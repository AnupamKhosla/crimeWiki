Run notes for a Haiku rewrite run (owner approved, 10 October 2026: "ok let us have explitcit instruction to haikus to stay under 100k and spawn 10 subagents to write 10 new posts"). Ten topics, 258-267, ten writers at once. Follow pipeline/rewrite_prompt.md, with these changes:
- Owner's rule for this run: stay under 100,000 tokens of context, from your first call to your final report. See "Context budget" below.
- About 9 other writers are working on other topics at the same time; touch only your own topic's files. The WebSearch limit is shared: if it says the budget is used up, do not retry it.
- Subject first: before writing, confirm from the pages you opened that they are about the subject the title names (same person, group or event, matching dates and place). If you cannot confirm the subject, block with the reason.
- Search: as many WebSearch calls as needed within your context budget (below), from as many reputable sources as possible.
- Then fetch the Wikipedia page as a lead (rewrite_prompt.md step 4); fetchmany saves the pages it cites to wiki-leads.txt. Open the original pages yourself; they may be cited once opened.
- For a 403, dead or THIN page try https://web.archive.org/web/<original url>. If WebSearch fails with an error, you may use `python3 pipeline/search.py "<query>" 12`.
- Wikipedia mirrors (wikimili, grokipedia, bharatpedia, wikiwand, dbpedia, alchetron, everybodywiki and the like) are Wikipedia: never fetch or cite them.
- Prefer court and government records, university and museum pages, books on archive.org, and long-form journalism. Large PDFs: search by keyword; do not read whole.
- Keep the exact title. The 1,000-word floor of real, sourced content still holds; never pad. If you cannot reach it, block with the reason and what you tried.

## Context budget

Stay under 100,000 tokens of context. Plan for it: a few well-chosen searches, then the strongest 6 to 10 pages read with read.py --max 7000; never read a long file or page whole (use Read with offset/limit, Grep, or keyword search). Stop researching by about 65,000 tokens: writing the article, checking and fixing it take about 25,000 more. A guard checks your context size before every tool call:
- When it refuses a tool with "Research budget used", stop researching at once. Write the article from the screens already in your context, run check_new.py, fix with Edit, and log. Those steps stay allowed.
- When it refuses with "Context limit reached", call no more tools and send your final report.
- Never try to get round the guard (another tool, a chained command). Report in your final reply whether it stopped you.
