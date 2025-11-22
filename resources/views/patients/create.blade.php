{{-- Add New Patient - Individual Treatment Record (ITR) --}}
@extends('layouts.app')

@section('title', 'Add New Patient - ITR')
@section('page-title', 'Individual Treatment Record (ITR)')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Form Container with Wizard -->
        <div class="form-container wizard-container">
            <h2 class="form-title">4Non Communicable Diseases - Individual Treatment Record</h2>

            <!-- Wizard Steps Indicator -->
            <div class="wizard-steps">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Patient Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Disease Category</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Screening & Assessment</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">History & Eye Screening</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-label">Health Assessment</div>
                </div>
            </div>

            <!-- Wizard Content -->
            <div class="wizard-content">
                <form method="POST" action="{{ route('patients.store') }}" class="patient-form" id="patientWizardForm">
                    @csrf

                    <!-- Step 1: Patient Information -->
                    <div class="step-content active" data-step="1">
                        <div class="form-section section-patient-info">
                            <h3 class="section-header"><span class="section-indicator"></span>Patient Information</h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_registered">Date Registered *</label>
                                    <input type="date" id="date_registered" name="date_registered" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="patient_no">Patient No.</label>
                                    <input type="text" id="patient_no" name="patient_no" class="form-control"
                                        placeholder="Auto-generated">
                                </div>
                                <div class="form-group">
                                    <label for="sex">Sex *</label>
                                    <select id="sex" name="sex" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="M">Male (M)</option>
                                        <option value="F">Female (F)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="name">Name *</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Last Name, First Name, Middle Name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="birthday">Birthday *</label>
                                    <input type="date" id="birthday" name="birthday" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" id="contact_number" name="contact_number" class="form-control"
                                        placeholder="09XX-XXX-XXXX">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="address">Address *</label>
                                    <textarea id="address" name="address" class="form-control" rows="2" required
                                        placeholder="House No., Street, Purok, Barangay"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nhts_id_no">NHTS ID No.</label>
                                    <input type="text" id="nhts_id_no" name="nhts_id_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="pwd_id_no">PWD ID No.</label>
                                    <input type="text" id="pwd_id_no" name="pwd_id_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="phic_id_no">PHIC ID No.</label>
                                    <input type="text" id="phic_id_no" name="phic_id_no" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="4ps_cct_id_no">4PS/CCT ID No.</label>
                                    <input type="text" id="4ps_cct_id_no" name="4ps_cct_id_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="ethnic_group">Indigenous People Ethnic Group</label>
                                    <input type="text" id="ethnic_group" name="ethnic_group" class="form-control"
                                        placeholder="If applicable">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Non Communicable Disease Category -->
                    <div class="step-content" data-step="2">
                        <div class="form-section section-disease">
                            <h3 class="section-header"><span class="section-indicator"></span>Non Communicable Disease
                                Category
                                <small>(Date of registration of disease)</small>
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="diabetes_date">Diabetes Date</label>
                                    <input type="date" id="diabetes_date" name="diabetes_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="hypertension_date">Hypertension Date</label>
                                    <input type="date" id="hypertension_date" name="hypertension_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="copd_date">COPD Date</label>
                                    <input type="date" id="copd_date" name="copd_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="asthma_date">Asthma Date</label>
                                    <input type="date" id="asthma_date" name="asthma_date" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cataract_date">Cataract Date</label>
                                    <input type="date" id="cataract_date" name="cataract_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="eor_date">EOR Date</label>
                                    <input type="date" id="eor_date" name="eor_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="diabetic_retinopathy_date">Diabetic Retinopathy Date</label>
                                    <input type="date" id="diabetic_retinopathy_date" name="diabetic_retinopathy_date"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="other_eye_disease_date">Other Eye Disease Date</label>
                                    <input type="date" id="other_eye_disease_date" name="other_eye_disease_date"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="alcoholism_date">Alcoholism Date</label>
                                    <input type="date" id="alcoholism_date" name="alcoholism_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="substance_abuse_date">Substance Abuse Date</label>
                                    <input type="date" id="substance_abuse_date" name="substance_abuse_date"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="other_mental_disorders_date">Other Mental Disorders Date</label>
                                    <input type="date" id="other_mental_disorders_date" name="other_mental_disorders_date"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="at_risk_suicide_date">At Risk for Suicide Date</label>
                                    <input type="date" id="at_risk_suicide_date" name="at_risk_suicide_date"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: PhilPen Screening & Disability Assessment -->
                    <div class="step-content" data-step="3">
                        <div class="form-section section-screening">
                            <h3 class="section-header"><span class="section-indicator"></span>PhilPen Screening & Disability
                                Assessment</h3>
                            <div class="form-row">
                                <!-- PhilPen Screening (Left Column) -->
                                <div class="form-group" style="flex: 1;">
                                    <h4 class="subsection-header">PhilPen Screening</h4>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="philpen_date">Date</label>
                                            <input type="date" id="philpen_date" name="philpen_date" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Smoking Status</label>
                                            <div class="checkbox-group">
                                                <label><input type="checkbox" name="current_smoker" value="1"> Current
                                                    Smoker</label>
                                                <label><input type="checkbox" name="passive_smoker" value="1"> Passive
                                                    Smoker</label>
                                                <label><input type="checkbox" name="stopped_smoking" value="1"> Stopped
                                                    Smoking</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><input type="checkbox" name="drinks_alcohol" value="1"> Drinks
                                                Alcohol</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input type="checkbox" name="had_5_drinks" value="1"> Had 5 drinks or
                                                more in 1
                                                occasion
                                                in a month</label>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><input type="checkbox" name="dietary_risk_factors" value="1"> Dietary
                                                Risk
                                                Factors</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input type="checkbox" name="physical_inactivity" value="1"> Physical
                                                Inactivity</label>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="height">Height</label>
                                            <input type="text" id="height" name="height" class="form-control"
                                                placeholder="cm">
                                        </div>
                                        <div class="form-group">
                                            <label for="weight">Weight (kg)</label>
                                            <input type="text" id="weight" name="weight" class="form-control"
                                                placeholder="kg">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="waist_circumference">Waist Circumference</label>
                                            <input type="text" id="waist_circumference" name="waist_circumference"
                                                class="form-control" placeholder="cm">
                                        </div>
                                        <div class="form-group">
                                            <label for="bmi">BMI</label>
                                            <input type="text" id="bmi" name="bmi" class="form-control"
                                                placeholder="Auto-calculated">
                                        </div>
                                    </div>
                                </div>

                                <!-- Disability Assessment (Right Column) -->
                                <div class="form-group" style="flex: 1; margin-left: 20px;">
                                    <h4 class="subsection-header">Disability Assessment (WHO-DAS)</h4>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="who_das_date">Date</label>
                                            <input type="date" id="who_das_date" name="who_das_date" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="part1">Part 1</label>
                                            <select id="part1" name="part1" class="form-control">
                                                <option value="">Select</option>
                                                <option value="No difficulty">No Difficulty</option>
                                                <option value="Refer">Refer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="part2_score">Part 2: Total Disability Score</label>
                                            <input type="text" id="part2_score" name="part2_score" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="top1_domain">Top 1 Domain</label>
                                            <input type="text" id="top1_domain" name="top1_domain" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="top2_domain">Top 2 Domain</label>
                                            <input type="text" id="top2_domain" name="top2_domain" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="top3_domain">Top 3 Domain</label>
                                            <input type="text" id="top3_domain" name="top3_domain" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: History & Eye Screening -->
                    <div class="step-content" data-step="4">
                        <div class="form-section section-history">
                            <h3 class="section-header"><span class="section-indicator"></span>History & Eye Screening</h3>
                            <div class="form-row">
                                <!-- History (Left Column) -->
                                <div class="form-group" style="flex: 1;">
                                    <h4 class="subsection-header">History</h4>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="length_diabetes">Length of Diabetes</label>
                                            <input type="text" id="length_diabetes" name="length_diabetes"
                                                class="form-control" placeholder="mos or yrs">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="length_hypertension">Length of Hypertension</label>
                                            <input type="text" id="length_hypertension" name="length_hypertension"
                                                class="form-control" placeholder="mos or yrs">
                                        </div>
                                    </div>
                                </div>

                                <!-- Eye Screening (Right Column) -->
                                <div class="form-group" style="flex: 1; margin-left: 20px;">
                                    <h4 class="subsection-header">Eye Screening</h4>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Symptoms</label>
                                            <div class="checkbox-group">
                                                <label><input type="checkbox" name="floaters" value="1"> Floaters</label>
                                                <label><input type="checkbox" name="blurred_vision" value="1"> Blurred
                                                    Vision</label>
                                                <label><input type="checkbox" name="fluctuating_vision" value="1">
                                                    Fluctuating
                                                    Vision</label>
                                                <label><input type="checkbox" name="impaired_color_vision" value="1">
                                                    Impaired Color
                                                    Vision</label>
                                                <label><input type="checkbox" name="dark_empty_areas" value="1"> Dark/Empty
                                                    Areas of
                                                    Vision</label>
                                                <label><input type="checkbox" name="vision_loss" value="1"> Vision
                                                    Loss</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="visual_acuity_left">Visual Acuity Test Result (Left Eye)</label>
                                            <input type="text" id="visual_acuity_left" name="visual_acuity_left"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="visual_acuity_right">Visual Acuity Test Result (Right Eye)</label>
                                            <input type="text" id="visual_acuity_right" name="visual_acuity_right"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ophthalmoscopy_results">Ophthalmoscopy Results</label>
                                            <textarea id="ophthalmoscopy_results" name="ophthalmoscopy_results"
                                                class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Initial Visit Assessment -->
                    <div class="step-content" data-step="5">
                        <div class="form-section section-assessment">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h3 class="section-header" style="margin: 0;"><span class="section-indicator"></span>Initial
                                    Visit
                                    Assessment</h3>
                                <button type="button" class="btn btn-success btn-sm" id="addAssessment">
                                    <i class="bi bi-plus-circle"></i> Add Assessment
                                </button>
                            </div>

                            <div id="assessmentsContainer">
                                <!-- Assessment Entry 1 -->
                                <div class="assessment-entry"
                                    style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                    <div class="form-row">
                                        <!-- Date/Monitoring Parameters (Left Column) -->
                                        <div class="form-group" style="flex: 1;">
                                            <h4>Monitoring Parameters</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Date / Monitoring
                                                Parameters</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Date:</label>
                                                    <input type="date" name="assessments[0][date]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Age:</label>
                                                    <input type="text" name="assessments[0][age]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>CVD Risk:</label>
                                                    <input type="text" name="assessments[0][cvd_risk]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>BP: (Systolic)</label>
                                                    <input type="text" name="assessments[0][bp_systolic]"
                                                        class="form-control" placeholder="mmHg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>BP: (Diastolic)</label>
                                                    <input type="text" name="assessments[0][bp_diastolic]"
                                                        class="form-control" placeholder="mmHg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Wt:</label>
                                                    <input type="text" name="assessments[0][wt]" class="form-control"
                                                        placeholder="kg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Ht:</label>
                                                    <input type="text" name="assessments[0][ht]" class="form-control"
                                                        placeholder="cm">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>FBS/RBS:</label>
                                                    <input type="text" name="assessments[0][fbs_rbs]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Lipid Profile:</label>
                                                    <input type="text" name="assessments[0][lipid_profile]"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Urine Ketones:</label>
                                                    <input type="text" name="assessments[0][urine_ketones]"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Urine Protein:</label>
                                                    <input type="text" name="assessments[0][urine_protein]"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Foot Check:</label>
                                                    <input type="text" name="assessments[0][foot_check]"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chief Complaint / History / Physical Examination / Diagnosis (Middle Column) -->
                                        <div class="form-group" style="flex: 1; margin-left: 20px;">
                                            <h4>Chief Complaint / Diagnosis</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Chief Complaint /
                                                History
                                                /
                                                Physical Examination / Diagnosis</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Chief Complaint:</label>
                                                    <textarea name="assessments[0][chief_complaint]" class="form-control"
                                                        rows="3"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>History / Physical Examination:</label>
                                                    <textarea name="assessments[0][history_physical]" class="form-control"
                                                        rows="7"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Management (Right Column) -->
                                        <div class="form-group" style="flex: 1; margin-left: 20px;">
                                            <h4>Management</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Management</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Management Plan:</label>
                                                    <textarea name="assessments[0][management]" class="form-control"
                                                        rows="12"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>

        <!-- Wizard Navigation Buttons -->
        <div class="wizard-buttons">
            <a href="{{ route('patients.index') }}" class="btn btn-cancel">Cancel</a>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-prev" id="prevBtn" onclick="changeStep(-1)">← Previous</button>
                <button type="button" class="btn btn-next" id="nextBtn" onclick="changeStep(1)">Next →</button>
                <button type="submit" class="btn btn-submit" id="submitBtn" form="patientWizardForm"
                    style="display: none;">Save Patient Record</button>
            </div>
        </div>
    </div>

    </div>

    <script>
        // Multi-step wizard functionality
        let currentStep = 1;
        const totalSteps = 5;

        function changeStep(direction) {
            const newStep = currentStep + direction;

            if (newStep < 1 || newStep > totalSteps) return;

            // Hide current step
            document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');

            // Show next step
            currentStep = newStep;
            document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');

            // Mark previous steps as completed
            document.querySelectorAll('.step').forEach(step => {
                const stepNum = parseInt(step.getAttribute('data-step'));
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                } else {
                    step.classList.remove('completed');
                }
            });

            // Update button visibility
            updateButtons();

            // Scroll to top of form
            document.querySelector('.wizard-content').scrollTop = 0;
        }

        function updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Show/hide previous button
            prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
            prevBtn.disabled = currentStep === 1;

            // Show next button on steps 1-4, hide on step 5
            nextBtn.style.display = currentStep === totalSteps ? 'none' : 'block';
            nextBtn.disabled = currentStep === totalSteps;

            // Show submit button only on step 5
            submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateButtons();

            // Make steps clickable (optional)
            document.querySelectorAll('.step').forEach(step => {
                step.addEventListener('click', function () {
                    const stepNum = parseInt(this.getAttribute('data-step'));
                    if (stepNum < currentStep) {
                        // Allow going back to previous steps
                        while (currentStep > stepNum) {
                            changeStep(-1);
                        }
                    }
                });
            });
        });

        // JavaScript to handle dynamic assessment forms
        let assessmentCount = 1;

        // Add new assessment form
        document.getElementById('addAssessment').addEventListener('click', function () {
            const container = document.getElementById('assessmentsContainer');
            const newAssessment = document.createElement('div');
            newAssessment.className = 'assessment-entry';
            newAssessment.style.cssText = 'border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;';

            newAssessment.innerHTML = `
                                    <div class="form-row">
                                        <!-- Date/Monitoring Parameters (Left Column) -->
                                        <div class="form-group" style="flex: 1;">
                                            <h4>Monitoring Parameters</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Date / Monitoring Parameters</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Date:</label>
                                                    <input type="date" name="assessments[${assessmentCount}][date]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Age:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][age]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>CVD Risk:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][cvd_risk]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>BP: (Systolic)</label>
                                                    <input type="text" name="assessments[${assessmentCount}][bp_systolic]" class="form-control" placeholder="mmHg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>BP: (Diastolic)</label>
                                                    <input type="text" name="assessments[${assessmentCount}][bp_diastolic]" class="form-control" placeholder="mmHg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Wt:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][wt]" class="form-control" placeholder="kg">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Ht:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][ht]" class="form-control" placeholder="cm">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>FBS/RBS:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][fbs_rbs]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Lipid Profile:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][lipid_profile]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Urine Ketones:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][urine_ketones]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Urine Protein:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][urine_protein]" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Foot Check:</label>
                                                    <input type="text" name="assessments[${assessmentCount}][foot_check]" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chief Complaint / History / Physical Examination / Diagnosis (Middle Column) -->
                                        <div class="form-group" style="flex: 1; margin-left: 20px;">
                                            <h4>Chief Complaint / Diagnosis</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Chief Complaint / History / Physical Examination / Diagnosis</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Chief Complaint:</label>
                                                    <textarea name="assessments[${assessmentCount}][chief_complaint]" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>History / Physical Examination:</label>
                                                    <textarea name="assessments[${assessmentCount}][history_physical]" class="form-control" rows="7"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Management (Right Column) -->
                                        <div class="form-group" style="flex: 1; margin-left: 20px;">
                                            <h4>Management</h4>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Management</p>

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label>Management Plan:</label>
                                                    <textarea name="assessments[${assessmentCount}][management]" class="form-control" rows="12"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

            container.appendChild(newAssessment);
            assessmentCount++;
        });
    </script>
@endsection