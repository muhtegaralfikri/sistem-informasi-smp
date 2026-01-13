<x-app-layout>


    <div x-data="assessmentsPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Weight Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="stat in weightStats" :key="stat.mapel">
                    <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase font-semibold text-gray-500">Bobot</div>
                                <div class="text-lg font-bold text-gray-900" x-text="stat.mapel"></div>
                            </div>
                            <span class="text-sm font-semibold px-2 py-1 rounded-md" :class="stat.is_complete ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'">
                                <span x-text="stat.total_weight + '%'"></span>
                            </span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="item in stat.assessments" :key="item.id">
                                <div class="flex items-center justify-between text-sm text-gray-700">
                                    <span x-text="item.title"></span>
                                    <span x-text="item.weight + '%'"></span>
                                </div>
                            </template>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full" :class="stat.is_complete ? 'bg-green-500' : 'bg-amber-400'" :style="`width: ${stat.total_weight}%`"></div>
                        </div>
                        <p class="text-xs" :class="stat.is_complete ? 'text-green-600' : 'text-amber-600'">
                            <span x-text="stat.is_complete ? 'Bobot lengkap (100%)' : `Sisa ${100 - stat.total_weight}%`"></span>
                        </p>
                    </div>
                </template>
            </div>

            <!-- Create Assessment -->
            <x-card>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Buat Penilaian Baru</h3>
                            <p class="text-sm text-gray-500">Tambah tugas, ulangan, UTS, atau UAS</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas - Mapel</label>
                            <select x-model="newForm.class_subject_id" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Pilih Kelas-Mapel</option>
                                <template x-for="cs in classSubjects" :key="cs.id">
                                    <option :value="cs.id" x-text="`${cs.class} - ${cs.subject}`"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                            <input type="text" x-model="newForm.title" placeholder="Contoh: UTS 1" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select x-model="newForm.type" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="tugas">Tugas</option>
                                <option value="uh">Ulangan Harian</option>
                                <option value="uts">UTS</option>
                                <option value="uas">UAS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%)</label>
                            <input type="number" x-model="newForm.weight" min="1" max="100" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Maksimum</label>
                            <input type="number" x-model="newForm.max_score" min="1" max="100" value="100" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tenggat</label>
                            <input type="date" x-model="newForm.due_date" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <p x-show="newError" class="text-red-600 text-sm" x-text="newError"></p>
                        <p x-show="weightWarning" class="text-amber-600 text-sm" x-text="weightWarning"></p>
                        <button @click="createAssessment" :disabled="loading" class="ml-auto px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!loading">Buat Penilaian</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                    </div>
                </div>
            </x-card>

            <!-- Assessments List -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Penilaian</h3>
                        <p class="text-sm text-gray-500">Kelola semua penilaian yang telah dibuat</p>
                    </div>
                    <button @click="loadAssessments" class="px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Refresh</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas / Mapel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bobot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Max</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="item in assessments" :key="item.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-semibold" x-text="item.classSubject?.classRoom?.name || '-'"></div>
                                        <div class="text-xs text-gray-500" x-text="item.classSubject?.subject?.name || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="item.title"></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 capitalize" x-text="item.type"></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="item.weight + '%'"></td>
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="item.max_score"></td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-full bg-green-500" :style="`width: ${item.progress || 0}%`"></div>
                                            </div>
                                            <span class="text-xs text-gray-600" x-text="`${item.graded_count || 0}/${item.total_students || 0}`"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button @click="selectAssessment(item)" class="text-indigo-600 hover:text-indigo-900 font-medium">Input Nilai</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Grade Input Section -->
            <x-card x-show="selectedAssessment.id">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Input Nilai</h3>
                            <p class="text-sm text-gray-500" x-text="`${selectedAssessment.classSubject?.classRoom?.name} - ${selectedAssessment.classSubject?.subject?.name} - ${selectedAssessment.title}`"></p>
                            <p class="text-xs text-gray-500">Max Score: <span x-text="selectedAssessment.max_score"></span></p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Norm</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="student in students" :key="student.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="student.name"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="student.nis"></td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <input type="number" step="0.01" :min="0" :max="selectedAssessment.max_score || 100"
                                                   class="w-24 rounded-md border-gray-200 text-sm"
                                                   :value="getGrade(student.id)"
                                                   @input="updateGrade(student.id, $event.target.value)">
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="calculateNormalized(getGrade(student.id))"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">
                            <span x-text="`${gradedCount}/${students.length} siswa dinilai`"></span>
                        </p>
                        <button @click="saveGrades" :disabled="loading"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!loading">Simpan Nilai</span>
                            <span x-show="loading">Menyimpan...</span>
                        </button>
                    </div>
                    <p x-show="gradeError" class="text-red-600 text-sm" x-text="gradeError"></p>
                    <p x-show="gradeSuccess" class="text-green-600 text-sm" x-text="gradeSuccess"></p>
                </div>
            </x-card>

            <!-- Final Scores Section -->
            <x-card x-show="selectedClassSubject">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Nilai Akhir</h3>
                        <p class="text-sm text-gray-500" x-text="`${selectedClassSubject.class} - ${selectedClassSubject.subject}`"></p>
                    </div>
                    <button @click="calculateFinalScores" :disabled="loading"
                            class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 disabled:opacity-50">
                        Hitung Nilai Akhir
                    </button>
                </div>

                <div x-show="finalScores.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Akhir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="fs in finalScores" :key="fs.student_id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="fs.student_name"></td>
                                    <td class="px-6 py-4 text-sm text-gray-600" x-text="fs.nis"></td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900" x-text="fs.final_score"></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold"
                                              :class="{
                                                  'bg-green-50 text-green-700': fs.predicate === 'A' || fs.predicate === 'B',
                                                  'bg-amber-50 text-amber-700': fs.predicate === 'C',
                                                  'bg-red-50 text-red-700': fs.predicate === 'D' || fs.predicate === 'E'
                                              }" x-text="fs.predicate"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <p x-show="finalScoreError" class="text-red-600 text-sm" x-text="finalScoreError"></p>
            </x-card>
        </div>
    </div>

    <script>
        function assessmentsPage() {
            return {
                loading: false,
                assessments: [],
                classSubjects: [],
                students: [],
                weightStats: [],
                finalScores: [],

                newForm: {
                    class_subject_id: '',
                    title: '',
                    type: 'tugas',
                    weight: '',
                    max_score: 100,
                    due_date: '',
                },

                selectedAssessment: {},
                selectedClassSubject: null,
                grades: {},

                newError: null,
                weightWarning: null,
                gradeError: null,
                gradeSuccess: null,
                finalScoreError: null,
                gradedCount: 0,

                async init() {
                    await this.loadClassSubjects();
                    await this.loadAssessments();
                    await this.loadWeightStats();
                },

                async loadClassSubjects() {
                    try {
                        const response = await fetch('/admin/class-subjects?include=class,subject', {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.classSubjects = data.data || data;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async loadAssessments() {
                    try {
                        const response = await fetch('/admin/assessments', {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (response.ok) {
                            this.assessments = await response.json();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async loadWeightStats() {
                    // Load weight stats for each class-subject
                    for (const cs of this.classSubjects) {
                        try {
                            const response = await fetch(`/admin/class-subjects/${cs.id}/assessments`, {
                                headers: { 'Accept': 'application/json' },
                            });
                            if (response.ok) {
                                const data = await response.json();
                                this.weightStats.push({
                                    mapel: `${data.class_subject?.classRoom?.name} - ${data.class_subject?.subject?.name}`,
                                    total_weight: data.weight_summary?.total_weight || 0,
                                    is_complete: data.weight_summary?.is_complete || false,
                                    assessments: data.assessments || [],
                                });
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                },

                async createAssessment() {
                    this.loading = true;
                    this.newError = null;
                    this.weightWarning = null;

                    try {
                        const response = await fetch('/admin/assessments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(this.newForm),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal membuat penilaian');
                        }

                        this.assessments.unshift(data);
                        this.newForm = {
                            class_subject_id: '',
                            title: '',
                            type: 'tugas',
                            weight: '',
                            max_score: 100,
                            due_date: '',
                        };
                        await this.loadWeightStats();
                    } catch (e) {
                        this.newError = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async selectAssessment(assessment) {
                    this.selectedAssessment = assessment;
                    this.grades = {};
                    this.gradedCount = 0;
                    this.gradeSuccess = null;

                    // Load students for the class
                    const classId = assessment.classSubject?.classRoom?.id;
                    if (classId) {
                        await this.loadStudents(classId);
                    }

                    // Load existing grades
                    if (assessment.grades) {
                        assessment.grades.forEach(grade => {
                            this.grades[grade.student_id] = grade.score;
                        });
                        this.gradedCount = assessment.grades.length;
                    }

                    // Set selected class subject for final scores
                    this.selectedClassSubject = {
                        id: assessment.classSubject?.id,
                        class: assessment.classSubject?.classRoom?.name,
                        subject: assessment.classSubject?.subject?.name,
                    };
                },

                async loadStudents(classId) {
                    try {
                        const response = await fetch(`/admin/students?class_id=${classId}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.students = data.data || data;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async saveGrades() {
                    this.loading = true;
                    this.gradeError = null;
                    this.gradeSuccess = null;

                    const gradesArray = Object.entries(this.grades).map(([student_id, score]) => ({
                        student_id: parseInt(student_id),
                        score: parseFloat(score) || 0,
                    }));

                    try {
                        const response = await fetch(`/admin/assessments/${this.selectedAssessment.id}/grades`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ grades: gradesArray }),
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.message || 'Gagal menyimpan nilai');
                        }

                        const data = await response.json();
                        this.grades = {};
                        if (data.grades) {
                            data.grades.forEach(grade => {
                                this.grades[grade.student_id] = grade.score;
                            });
                        }
                        this.gradedCount = gradesArray.length;
                        this.gradeSuccess = 'Nilai berhasil disimpan!';
                        await this.loadAssessments();
                    } catch (e) {
                        this.gradeError = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async calculateFinalScores() {
                    if (!this.selectedClassSubject?.id) return;

                    this.loading = true;
                    this.finalScoreError = null;

                    try {
                        const response = await fetch(`/admin/class-subjects/${this.selectedClassSubject.id}/final-scores`, {
                            headers: { 'Accept': 'application/json' },
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghitung nilai akhir');
                        }

                        this.finalScores = data.final_scores || [];
                    } catch (e) {
                        this.finalScoreError = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                getGrade(studentId) {
                    return this.grades[studentId] || '';
                },

                updateGrade(studentId, value) {
                    this.grades[studentId] = value;
                },

                calculateNormalized(score) {
                    if (!score || !this.selectedAssessment.max_score) return '-';
                    const norm = (score / this.selectedAssessment.max_score) * 100;
                    return norm.toFixed(1);
                },
            };
        }
    </script>
</x-app-layout>