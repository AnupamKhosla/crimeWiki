Run notes for this batch (owner approved, 7 October 2026; 30 topics: 160-189). Follow pipeline/rewrite_prompt.md, with these changes:
- Subject first: before writing, confirm from the pages you opened that they are about the subject the title names (the same person, group or event, with matching dates and place). A namesake is a different subject. If you cannot confirm the subject, block with the reason.
- Search budget (the WebSearch tool is limited per session and is shared by every writer in this run): exactly 2 WebSearch calls, for your own research first. Then fetch the Wikipedia page as a lead (rewrite_prompt.md step 4); fetchmany saves the pages it cites to wiki-leads.txt. Take further sources from wiki-leads.txt: open the original pages yourself; they may be cited once opened. For any remaining gap, up to 3 searches with `python3 pipeline/search.py "<query>" 12` (Brave, then DuckDuckGo, then Bing; it waits and retries once if no engine answers). No more WebSearch calls after the first 2, even if one returned little.
- Fetching: up to 3 fetchmany calls of at most 10 URLs each, plus the Wikipedia lead fetch.
- For a page that returns 403 or THIN, try its Internet Archive copy: https://web.archive.org/web/2024/<original url>.
- Wikipedia mirrors (wikimili, grokipedia, bharatpedia, wikiwand, dbpedia, alchetron, everybodywiki and the like) are Wikipedia: never fetch or cite them as sources.
- Prefer court and government records, university and museum pages, books on archive.org, and long-form journalism.
- Large PDFs: search them by keyword; do not read them whole.
- Keep the exact title. The 1,000-word floor of real, sourced content still holds; never pad. If you cannot reach it, block with the reason.
- About 29 other writers are working on other topics at the same time; touch only your own topic's files.
