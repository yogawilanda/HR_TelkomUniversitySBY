# Safe Dynamic Forms v2 (No JS Break)
Status: In Progress | v2 Approved - Keep JS/$isPerkuliahan

## Steps:

### 1. **✅ Create TODO**

### 2. **✅ Controller** DetilPengajuanController.php
   - ✓ $specialCases array
   - ✓ $specialFields lookup
   - ✓ Passed to view

### 3. **View** generic_form.blade.php
   - Replace hardcoded 3 fields with @foreach($specialFields)
   - Keep @if($isPerkuliahan), JS, preview id unchanged

### 4. **Test**
   - Perkuliahan ID=3: fields + calc works

### 5. **Scale**
   - Add more $specialCases entries

Next: Controller edit.

