"use client"

import { useEffect, useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { FileText, Users, Plus, Edit, Trash2, Eye, Bell, BookOpen } from "lucide-react"

interface User {
  email: string
  role: string
  department?: string
}

export default function CITDashboard() {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Check if user is logged in and has CIT department access
    const userData = localStorage.getItem("user")
    if (userData) {
      const parsedUser = JSON.parse(userData)
      if (parsedUser.role === "department" && parsedUser.department === "cit") {
        setUser(parsedUser)
      } else {
        // Redirect unauthorized users
        window.location.href = "/login"
      }
    } else {
      window.location.href = "/login"
    }
    setLoading(false)
  }, [])

  const handleLogout = () => {
    localStorage.removeItem("user")
    window.location.href = "/login"
  }

  if (loading) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>
  }

  if (!user) {
    return null
  }

  const stats = [
    { title: "CIT Students", value: "450", icon: Users, color: "text-blue-600" },
    { title: "Faculty Members", value: "25", icon: Users, color: "text-green-600" },
    { title: "Active Programs", value: "3", icon: BookOpen, color: "text-purple-600" },
    { title: "Department News", value: "12", icon: FileText, color: "text-orange-600" },
  ]

  const departmentNews = [
    { id: 1, title: "New Programming Lab Equipment", status: "Published", date: "2024-01-15" },
    { id: 2, title: "Industry Partnership with Tech Companies", status: "Draft", date: "2024-01-14" },
    { id: 3, title: "Student Hackathon Results", status: "Published", date: "2024-01-13" },
  ]

  const programs = [
    { name: "Bachelor of Science in Computer Science", students: 200, faculty: 12 },
    { name: "Bachelor of Science in Information Technology", students: 180, faculty: 10 },
    { name: "Certificate in Web Development", students: 70, faculty: 3 },
  ]

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">CIT Department Dashboard</h1>
              <p className="text-gray-600">Computer and Information Technology Department</p>
            </div>
            <div className="flex items-center space-x-4">
              <Button variant="outline" size="sm">
                <Bell className="h-4 w-4 mr-2" />
                Notifications
              </Button>
              <Button variant="outline" size="sm" onClick={handleLogout}>
                Logout
              </Button>
            </div>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-8">
        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <Card key={index}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-gray-600 mb-1">{stat.title}</p>
                    <p className="text-3xl font-bold">{stat.value}</p>
                  </div>
                  <stat.icon className={`h-8 w-8 ${stat.color}`} />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Main Content */}
        <Tabs defaultValue="overview" className="space-y-6">
          <TabsList>
            <TabsTrigger value="overview">Overview</TabsTrigger>
            <TabsTrigger value="news">Department News</TabsTrigger>
            <TabsTrigger value="programs">Programs</TabsTrigger>
            <TabsTrigger value="students">Students</TabsTrigger>
          </TabsList>

          <TabsContent value="overview" className="space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Department News */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center justify-between">
                    Recent Department News
                    <Button size="sm">
                      <Plus className="h-4 w-4 mr-2" />
                      Add News
                    </Button>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {departmentNews.map((news) => (
                      <div key={news.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                          <h4 className="font-medium">{news.title}</h4>
                          <p className="text-sm text-gray-600">{news.date}</p>
                        </div>
                        <div className="flex items-center space-x-2">
                          <Badge variant={news.status === "Published" ? "default" : "secondary"}>{news.status}</Badge>
                          <Button size="sm" variant="ghost">
                            <Edit className="h-4 w-4" />
                          </Button>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Programs Overview */}
              <Card>
                <CardHeader>
                  <CardTitle>Programs Overview</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {programs.map((program, index) => (
                      <div key={index} className="p-3 border rounded-lg">
                        <h4 className="font-medium mb-2">{program.name}</h4>
                        <div className="flex justify-between text-sm text-gray-600">
                          <span>Students: {program.students}</span>
                          <span>Faculty: {program.faculty}</span>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="news">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  CIT Department News
                  <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Create News Article
                  </Button>
                </CardTitle>
                <CardDescription>Manage news and announcements specific to the CIT Department</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {departmentNews.map((news) => (
                    <div key={news.id} className="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 className="font-medium">{news.title}</h4>
                        <p className="text-sm text-gray-600">Published on {news.date}</p>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Badge variant={news.status === "Published" ? "default" : "secondary"}>{news.status}</Badge>
                        <Button size="sm" variant="ghost">
                          <Eye className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="programs">
            <Card>
              <CardHeader>
                <CardTitle>CIT Programs</CardTitle>
                <CardDescription>
                  Manage programs offered by the Computer and Information Technology Department
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {programs.map((program, index) => (
                    <div key={index} className="flex items-center justify-between p-4 border rounded-lg">
                      <div>
                        <h4 className="font-medium">{program.name}</h4>
                        <p className="text-sm text-gray-600">
                          {program.students} students • {program.faculty} faculty members
                        </p>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Button size="sm" variant="ghost">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button size="sm" variant="ghost">
                          <Eye className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="students">
            <Card>
              <CardHeader>
                <CardTitle>Student Management</CardTitle>
                <CardDescription>View and manage CIT department students</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="text-center py-8">
                  <Users className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">Student Management</h3>
                  <p className="text-gray-600 mb-4">
                    This section would contain student enrollment data, grades, and academic records.
                  </p>
                  <Button>View Student Records</Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}
