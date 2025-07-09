"use client"

import type React from "react"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { GraduationCap, Shield, Users } from "lucide-react"

export default function LoginPage() {
  const [isLoading, setIsLoading] = useState(false)

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsLoading(true)

    const formData = new FormData(e.target as HTMLFormElement)
    const email = formData.get("email") as string
    const password = formData.get("password") as string
    const department = formData.get("department") as string

    // Demo credentials
    const demoCredentials = {
      "admin@sjcsi.edu.ph": { password: "admin123", role: "admin", redirect: "/admin/dashboard" },
      "cit@sjcsi.edu.ph": {
        password: "cit123",
        role: "department",
        department: "cit",
        redirect: "/department/cit/dashboard",
      },
      "cba@sjcsi.edu.ph": {
        password: "cba123",
        role: "department",
        department: "cba",
        redirect: "/department/cba/dashboard",
      },
      "coa@sjcsi.edu.ph": {
        password: "coa123",
        role: "department",
        department: "coa",
        redirect: "/department/coa/dashboard",
      },
      "caste@sjcsi.edu.ph": {
        password: "caste123",
        role: "department",
        department: "caste",
        redirect: "/department/caste/dashboard",
      },
      "cje@sjcsi.edu.ph": {
        password: "cje123",
        role: "department",
        department: "cje",
        redirect: "/department/cje/dashboard",
      },
      "shs@sjcsi.edu.ph": {
        password: "shs123",
        role: "department",
        department: "shs",
        redirect: "/department/shs/dashboard",
      },
      "jhs@sjcsi.edu.ph": {
        password: "jhs123",
        role: "department",
        department: "jhs",
        redirect: "/department/jhs/dashboard",
      },
    }

    setTimeout(() => {
      const user = demoCredentials[email as keyof typeof demoCredentials]

      if (user && user.password === password) {
        // Store user info in localStorage (in a real app, use proper session management)
        localStorage.setItem(
          "user",
          JSON.stringify({
            email,
            role: user.role,
            department: user.department || null,
          }),
        )

        // Redirect based on role
        window.location.href = user.redirect
      } else {
        alert("Invalid credentials. Please use the demo credentials provided above.")
      }

      setIsLoading(false)
    }, 1000)
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="flex justify-center mb-4">
          <img
            className="mx-auto mb-4 w-20 h-20 rounded-full shadow-lg"
            alt="School logo"
            src="./sjcsi-logo.png"
          />
          </div>
          <h1 className="text-2xl font-bold text-gray-900">SJCSI Portal</h1>
          <p className="text-gray-600">Sign in to access your account</p>
        </div>

        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
          <h3 className="font-semibold text-yellow-800 mb-2">Demo Credentials</h3>
          <div className="text-sm text-yellow-700 space-y-1">
            <p>
              <strong>Admin:</strong> admin@sjcsi.edu.ph / admin123
            </p>
            <p>
              <strong>Department (CIT):</strong> cit@sjcsi.edu.ph / cit123
            </p>
            <p>
              <strong>Department (CBA):</strong> cba@sjcsi.edu.ph / cba123
            </p>
          </div>
        </div>

        <Tabs defaultValue="admin" className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="admin" className="flex items-center space-x-2">
              <Shield className="h-4 w-4" />
              <span>Admin</span>
            </TabsTrigger>
            <TabsTrigger value="department" className="flex items-center space-x-2">
              <Users className="h-4 w-4" />
              <span>Department</span>
            </TabsTrigger>
          </TabsList>

          <TabsContent value="admin">
            <Card>
              <CardHeader>
                <CardTitle>Admin Login</CardTitle>
                <CardDescription>Access administrative functions and manage the entire website</CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleLogin} className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="admin-email">Email</Label>
                    <Input id="admin-email" name="email" type="email" placeholder="admin@sjcsi.edu.ph" required />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="admin-password">Password</Label>
                    <Input id="admin-password" name="password" type="password" required />
                  </div>
                  <Button type="submit" className="w-full" disabled={isLoading}>
                    {isLoading ? "Signing in..." : "Sign In as Admin"}
                  </Button>
                </form>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="department">
            <Card>
              <CardHeader>
                <CardTitle>Department Login</CardTitle>
                <CardDescription>
                  Access your department's section and manage department-specific content
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleLogin} className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="department">Department</Label>
                    <Select name="department">
                      <SelectTrigger>
                        <SelectValue placeholder="Select your department" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="caste">CASTE Department</SelectItem>
                        <SelectItem value="cit">CIT Department</SelectItem>
                        <SelectItem value="coa">COA Department</SelectItem>
                        <SelectItem value="cba">CBA Department</SelectItem>
                        <SelectItem value="cje">CJE Department</SelectItem>
                        <SelectItem value="shs">SHS Department</SelectItem>
                        <SelectItem value="jhs">JHS Department</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="dept-email">Email</Label>
                    <Input id="dept-email" name="email" type="email" placeholder="department@sjcsi.edu.ph" required />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="dept-password">Password</Label>
                    <Input id="dept-password" name="password" type="password" required />
                  </div>
                  <Button type="submit" className="w-full" disabled={isLoading}>
                    {isLoading ? "Signing in..." : "Sign In to Department"}
                  </Button>
                </form>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>

        <div className="mt-6 text-center">
          <p className="text-sm text-gray-600">
            Forgot your password?{" "}
            <a href="#" className="text-blue-600 hover:underline">
              Contact IT Support
            </a>
          </p>
        </div>
      </div>
    </div>
  )
}
