---
marp: true
---

# Project 1 Grading and Submission

Total Points: 200

## Grading Policy

- Each question is graded on a **0–100% scale**:
  - **100%** if your answer is correct or reanable.  
  - **0%** if your answer is missing or entirely incorrect.  
  - **0%** if your answer is too low in quality.
  - **Partial credit** may be awarded for incomplete but reasonable answers.

---

## Submission Requirements

- You will create **Marp slides** to present your tutorial to your (future) team members.  
Your slides will also become part of your **portfolio**, shared on **GitHub & GitHub.io**.
- The slides should clearly explain how to use your REST APIs with enough detail for beginners.

---

### Guidelines

- **Use the provided Marp slide templates** in the `marp` directory.
- For each question:
  - Create a tutorial-style slide with **examples and explanations** (similar to my marp slides).  
  - Add **pictures or diagrams** if they help understanding.  
- You may refer to my slides in `module1/lecture`:
  - If you do, include a **comment** in your Marp file noting the reference  
    (e.g., file name and page number) to avoid plagiarism.  
- You may also **reuse your homework answers**, but cite them properly if necessary.

---

## Answer the Questions for Grading

For each question, select **T** if you completed the task (full points) or **F** if you did not (0 or partial points).

---

1. [T] Did you build a working **REST API system** in PHP & MySQL and make marp slides?  
   *(Max 120 points)*  

   - If not fully completed, what percentage of the task did you finish? (   % )

---

2. [T] Did you **upload your code to GitHub**?  
   *(Max 30 points)*  

   - If you did not upload to GitHub, what is the name of the **zipped file** you submitted? ( ________ )

---

3. [T] Did you create **Marp slides** for your presentation and place them in the presentation directory?  
   *(Max 30 points)*

---

4. [T] Did you copy both the **plan file (HW2)** and the **progress file (HW2)** into the plan directory?  
   *(Max 20 points)*

---

## Section A — REST APIs in PHP & MySQL (120 pts)

### 1) Describe your service (30 pts)

**Submit:** `marp/1_apis.md` — **Pages: 13+**  (replace with actual)

**Directions**

- Explain **why** you built this REST API and the **problem** it solves.
- List **each endpoint** and what it does.
- Replace *Page Count* with the real page count.
- **Scoring:** Full credit for clear explanations + endpoint list with **≥ 5 pages**; otherwise partial/zero.

---

### 2) How many APIs did you implement? (20 pts)

**Answer:** I implemented **13** REST APIs.

**Directions**

- You must implement **at least 10** endpoints for full credit.
- Replace **(*REST count*)** with your number.
- **Scoring:** **20**, up to 20.

---

### 3) Explain your MySQL database (30 pts)

**Submit:** `marp/3_mysql.md` — **Pages: 13** *Page Count*

**Directions**

- Describe your **SQLite table structure**.
- Identify **which APIs** use which tables.
- Replace *Page Count* with the real page count.
- **Scoring:** Full credit for clear schema + API usage with **≥ 3 pages**; otherwise partial/zero.

---

### 4) Show how to use your APIs (20 pts)

**Submit:**  

- Slides: `marp/4_example.md` — **Pages: 15+** *Page Count*  
- Source: all commands and HTML/JS in the `code/` directory

**Directions**

- Provide **curl commands** and **HTML/JavaScript** examples for **all** endpoints.
- Include **captured outputs** (responses, status codes) in slides.
- **Scoring:** Full credit for complete commands + outputs with **≥ 5 pages**; otherwise partial/zero.

---

### 5) Explain your security features (20 pts)

**Submit:** `marp/5_security.md` — **Pages: 5**

**Directions**

- Describe the **security mechanisms** you implemented for your REST APIs.
- State **which endpoints** use each security feature.
- Replace *Page Count* with the real page count.
- **Scoring:** Full credit for clear mapping of features to endpoints with **≥ 5 pages**; otherwise partial/zero.

---

### Section A — Subtotal (120 pts)

|                   Item |  Points  | Comment |
|-----------------------:|:--------:|---------|
| 1. Service description | (30/ 30) |         |
|     2. Total API count | (20/ 20) |         |
|   3. MySQL explanation | (20/ 30) |         |
|      4. Usage examples | (20/ 20) |         |
|   5. Security features | (20/ 20) |         |
|           **Subtotal** | **120/120** |         |

---

## Section B — GitHub Submission (30 pts)

**Answer one:**

- **GitHub Repo URL:** [(*https://github.com/Pbierley/FitnessTracker*)  ](https://github.com/Pbierley/FitnessTracker)
**OR**
- **Uploaded ZIP name (if no GitHub):** (*Your Zip File*)

**Directions**

- Replace *Your Link* with a link to your repo for **30 pts**.  
- If no repo, upload a ZIP of your `code/` directory and provide its name for **15 pts**.

---

**Scoring**

|          Item |  Points  | Comment |
|--------------:|:--------:|---------|
| GitHub or ZIP | (30/ 30) |         |
|  **Subtotal** | **30/30**|         |

---

## Section C — Presentation Slides (30 pts)

**Answer:**

- Presentation file in `presentation/` named `*yourname*_*yourAPIname*.md`  
- **Total pages:** (*30*)  
- **[T/F]** Covered **all 5 topics**: (1) PHP, (2) MySQL, (3) REST APIs, (4) Security, (5) Access via curl/HTML/JS

**Directions**

- Slides must enable a teammate to **understand and use** your APIs.
- You may reuse content from other answers or homework (cite where appropriate).
- **Scoring:** Full credit for covering all 5 topics with **≥ 15 pages**; otherwise partial/zero.

---

**Scoring**

|                     Item |  Points  | Comment |
|-------------------------:|:--------:|---------|
| REST API tutorial slides | (30/ 30) |         |
|             **Subtotal** | **30/30**|         |

---

## Section D — Plan & Progress (20 pts)

**Checklist (T/F)**

- I copied my original **`plan.md` (HW2)** to the `plan/` directory.  
- I recorded ongoing work in **`progress.md` (HW2)** as tasks were completed.  
- I copied **`progress.md`** to the `plan/` directory.  
- I added comments in `progress.md` explaining **any missed milestones**.

**Directions**

- Full credit requires **both files** present.
- **`progress.md`** must show a **timeline of progress** to earn points.

---

## Section E — Overall Completion

**[T]** I met **all** milestones from HW2.

**Directions**

- If **T**, select **100%**.  
- If **F**, select **80%**.  
  - You can still receive **100%** here if you included **clear reasons** in `progress.md` for any missed milestones.

---

## Final Rubric (200 pts)

| Category         | Points        | Comment |
|------------------|---------------|---------|
| A. REST APIs     | (120/ 120)     |         |
| B. GitHub        | (30/ 30)      |         |
| C. Presentation  | (30/ 30)      |         |
| D. Plan/Progress | (20/ 20)      |         |
| **Subtotal**     | **(200/ 200)** |         |
| Follow the Plan  | (100% / 100%)  |         |
| **Final Score**  | **(200/ 200)** |         |

---

## Submission Instructions

- Use **this rubric file** to guide your self-assessment and ensure completeness.  
- Verify that **all required files** are included:
  - REST API source code (`code/` directory or GitHub link)  
  - Marp slide decks (`marp/` and `presentation/` directories)  
  - Plan and progress files (`plan/` directory)  
- Compress the entire **project directory into a ZIP file**.  
- Upload and submit your ZIP file on **Canvas** by the deadline.  

---

### Notes

- All slides are part of your **public portfolio** (GitHub / GitHub Pages).
- Cite any reused materials (e.g., `module1/lecture`) with file name and page.
- Keep examples reproducible: include commands, code, and captured outputs.
