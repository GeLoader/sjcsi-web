import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { BookOpen, Clock, DollarSign, Award, FileText } from "lucide-react"

export default function AcademicPage() {
  const programs = {
    college: [
      {
        name: "Bachelor of Science in Computer Science",
        department: "CIT",
        duration: "4 years",
        units: "120 units",
        tuition: "₱25,000/semester",
        description: "Comprehensive program covering software development, algorithms, and computer systems.",
      },
      {
        name: "Bachelor of Science in Information Technology",
        department: "CIT",
        duration: "4 years",
        units: "120 units",
        tuition: "₱24,000/semester",
        description: "Focus on practical IT skills, networking, and system administration.",
      },
      {
        name: "Bachelor of Science in Business Administration",
        department: "CBA",
        duration: "4 years",
        units: "120 units",
        tuition: "₱22,000/semester",
        description: "Comprehensive business education with specializations in management and marketing.",
      },
      {
        name: "Bachelor of Science in Accountancy",
        department: "COA",
        duration: "4 years",
        units: "120 units",
        tuition: "₱26,000/semester",
        description: "Preparation for CPA licensure and careers in accounting and finance.",
      },
      {
        name: "Bachelor of Science in Criminal Justice Education",
        department: "CJE",
        duration: "4 years",
        units: "120 units",
        tuition: "₱23,000/semester",
        description: "Training for careers in law enforcement and criminal justice system.",
      },
      {
        name: "Bachelor of Science in Agriculture",
        department: "CASTE",
        duration: "4 years",
        units: "120 units",
        tuition: "₱21,000/semester",
        description: "Modern agricultural practices, crop science, and sustainable farming.",
      },
    ],
    shs: [
      {
        name: "Science, Technology, Engineering and Mathematics (STEM)",
        department: "SHS",
        duration: "2 years",
        units: "80 units",
        tuition: "₱15,000/semester",
        description: "Preparation for engineering, medicine, and science-related courses.",
      },
      {
        name: "Accountancy, Business and Management (ABM)",
        department: "SHS",
        duration: "2 years",
        units: "80 units",
        tuition: "₱15,000/semester",
        description: "Foundation for business, accounting, and management courses.",
      },
      {
        name: "Humanities and Social Sciences (HUMSS)",
        department: "SHS",
        duration: "2 years",
        units: "80 units",
        tuition: "₱15,000/semester",
        description: "Liberal arts education for social sciences and humanities.",
      },
      {
        name: "Technical-Vocational-Livelihood (TVL)",
        department: "SHS",
        duration: "2 years",
        units: "80 units",
        tuition: "₱16,000/semester",
        description: "Hands-on technical skills for immediate employment.",
      },
    ],
    jhs: [
      {
        name: "Junior High School Program",
        department: "JHS",
        duration: "4 years",
        units: "N/A",
        tuition: "₱12,000/semester",
        description: "Complete secondary education following K-12 curriculum.",
      },
    ],
  }

  const scholarships = [
    {
      name: "Academic Excellence Scholarship",
      coverage: "Full Tuition",
      requirements: "GPA of 1.5 or higher, Leadership activities",
      slots: "50 per year",
    },
    {
      name: "Financial Need Scholarship",
      coverage: "50% Tuition",
      requirements: "Family income below ₱200,000/year",
      slots: "100 per year",
    },
    {
      name: "Sports Scholarship",
      coverage: "Partial Tuition + Allowance",
      requirements: "Outstanding athletic performance",
      slots: "25 per year",
    },
    {
      name: "TESDA Scholarship",
      coverage: "Full Course Fee",
      requirements: "For TESDA programs only",
      slots: "Unlimited",
    },
  ]

  const admissionRequirements = {
    college: [
      "High School Diploma or equivalent",
      "Transcript of Records",
      "Certificate of Good Moral Character",
      "Medical Certificate",
      "2x2 ID Pictures (4 copies)",
      "Birth Certificate (NSO)",
      "Entrance Examination (if required)",
    ],
    shs: [
      "Junior High School Diploma",
      "Form 138 (Report Card)",
      "Certificate of Good Moral Character",
      "Medical Certificate",
      "2x2 ID Pictures (4 copies)",
      "Birth Certificate (NSO)",
    ],
    jhs: [
      "Elementary Diploma",
      "Form 138 (Report Card)",
      "Certificate of Good Moral Character",
      "Medical Certificate",
      "2x2 ID Pictures (4 copies)",
      "Birth Certificate (NSO)",
    ],
  }

  return (
    <div className="min-h-screen py-12">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-6">Academic Programs</h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Discover our comprehensive range of academic programs designed to prepare you for success in your chosen
            career path.
          </p>
        </div>

        {/* Programs Tabs */}
        <Tabs defaultValue="college" className="mb-16">
          <TabsList className="grid w-full grid-cols-3 max-w-md mx-auto mb-8">
            <TabsTrigger value="college">College</TabsTrigger>
            <TabsTrigger value="shs">Senior High</TabsTrigger>
            <TabsTrigger value="jhs">Junior High</TabsTrigger>
          </TabsList>

          <TabsContent value="college">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {programs.college.map((program, index) => (
                <Card key={index} className="hover:shadow-lg transition-shadow">
                  <CardHeader>
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <CardTitle className="text-lg mb-2">{program.name}</CardTitle>
                        <CardDescription>{program.description}</CardDescription>
                      </div>
                      <Badge variant="outline">{program.department}</Badge>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="grid grid-cols-2 gap-4 mb-4">
                      <div className="flex items-center space-x-2">
                        <Clock className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.duration}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <BookOpen className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.units}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <DollarSign className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.tuition}</span>
                      </div>
                    </div>
                    <Button className="w-full">Learn More</Button>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="shs">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {programs.shs.map((program, index) => (
                <Card key={index} className="hover:shadow-lg transition-shadow">
                  <CardHeader>
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <CardTitle className="text-lg mb-2">{program.name}</CardTitle>
                        <CardDescription>{program.description}</CardDescription>
                      </div>
                      <Badge variant="outline">{program.department}</Badge>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="grid grid-cols-2 gap-4 mb-4">
                      <div className="flex items-center space-x-2">
                        <Clock className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.duration}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <BookOpen className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.units}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <DollarSign className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.tuition}</span>
                      </div>
                    </div>
                    <Button className="w-full">Learn More</Button>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="jhs">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {programs.jhs.map((program, index) => (
                <Card key={index} className="hover:shadow-lg transition-shadow">
                  <CardHeader>
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <CardTitle className="text-lg mb-2">{program.name}</CardTitle>
                        <CardDescription>{program.description}</CardDescription>
                      </div>
                      <Badge variant="outline">{program.department}</Badge>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="grid grid-cols-2 gap-4 mb-4">
                      <div className="flex items-center space-x-2">
                        <Clock className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.duration}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <DollarSign className="h-4 w-4 text-gray-500" />
                        <span className="text-sm">{program.tuition}</span>
                      </div>
                    </div>
                    <Button className="w-full">Learn More</Button>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>
        </Tabs>

        
        {/* Admission Requirements */}
        <section className="mb-16">
          <div className="text-center mb-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">Admission Requirements</h2>
            <p className="text-gray-600 max-w-2xl mx-auto">
              Prepare your documents and requirements for a smooth enrollment process.
            </p>
          </div>

          <Tabs defaultValue="college" className="max-w-4xl mx-auto">
            <TabsList className="grid w-full grid-cols-3">
              <TabsTrigger value="college">College</TabsTrigger>
              <TabsTrigger value="shs">Senior High</TabsTrigger>
              <TabsTrigger value="jhs">Junior High</TabsTrigger>
            </TabsList>

            {Object.entries(admissionRequirements).map(([level, requirements]) => (
              <TabsContent key={level} value={level}>
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center space-x-2">
                      <FileText className="h-5 w-5" />
                      <span>{level.charAt(0).toUpperCase() + level.slice(1)} Requirements</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <ul className="space-y-2">
                      {requirements.map((requirement, index) => (
                        <li key={index} className="flex items-center space-x-2">
                          <div className="w-2 h-2 bg-blue-600 rounded-full"></div>
                          <span>{requirement}</span>
                        </li>
                      ))}
                    </ul>
                  </CardContent>
                </Card>
              </TabsContent>
            ))}
          </Tabs>
        </section>

        {/* Enrollment Process */}
        <section className="bg-gray-50 rounded-lg p-8">
          <div className="text-center mb-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">Enrollment Process</h2>
            <p className="text-gray-600">Follow these simple steps to secure your spot at SJCSI</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="text-center">
              <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-2xl font-bold text-blue-600">1</span>
              </div>
              <h3 className="font-semibold mb-2">Submit Application</h3>
              <p className="text-sm text-gray-600">Complete and submit your application form with required documents</p>
            </div>
            <div className="text-center">
              <div className="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-2xl font-bold text-green-600">2</span>
              </div>
              <h3 className="font-semibold mb-2">Take Entrance Exam</h3>
              <p className="text-sm text-gray-600">Schedule and take the entrance examination (if required)</p>
            </div>
            <div className="text-center">
              <div className="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-2xl font-bold text-purple-600">3</span>
              </div>
              <h3 className="font-semibold mb-2">Interview & Assessment</h3>
              <p className="text-sm text-gray-600">Attend interview and complete assessment process</p>
            </div>
            <div className="text-center">
              <div className="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-2xl font-bold text-orange-600">4</span>
              </div>
              <h3 className="font-semibold mb-2">Enroll & Pay</h3>
              <p className="text-sm text-gray-600">Complete enrollment and pay tuition fees</p>
            </div>
          </div>

          <div className="text-center mt-8">
            <Button size="lg" className="mr-4">
              Start Application
            </Button>
            <Button size="lg" variant="outline">
              Download Forms
            </Button>
          </div>
        </section>
      </div>
    </div>
  )
}
