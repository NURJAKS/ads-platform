package main

import (
    "github.com/gofiber/fiber/v2"
    "github.com/disintegration/imaging"
    "os"
)

func main() {
    app := fiber.New()

    app.Get("/health", func(c *fiber.Ctx) error {
        return c.SendString("OK")
    })

    app.Post("/process", func(c *fiber.Ctx) error {
        file, err := c.FormFile("image")
        if err != nil {
            return c.Status(400).SendString("Image file missing")
        }

        temp := "/tmp/" + file.Filename
        if err := c.SaveFile(file, temp); err != nil {
            return err
        }

        img, err := imaging.Open(temp)
        if err != nil {
            return err
        }

        resized := imaging.Resize(img, 800, 0, imaging.Lanczos)
        out := "/tmp/resized-" + file.Filename
        imaging.Save(resized, out)

        data, err := os.ReadFile(out)
        if err != nil {
            return err
        }

        c.Set("Content-Type", "image/jpeg")
        return c.Send(data)
    })

    app.Listen(":8080")
}
